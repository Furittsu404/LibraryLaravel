<?php

namespace App\Http\Controllers\Livewire\StudentReservationPage;

use App\Http\Controllers\Controller;
use App\Models\BlockedTimeSlot;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentReservationController extends Controller
{
    /**
     * Show the student reservation page
     */
    public function index()
    {
        $rooms = Room::where('is_available', true)->get();

        return view('student.reservations', compact('rooms'));
    }

    /**
     * Get available time slots for a specific room and date range
     */
    public function getAvailableSlots(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $slots = [];

        // Operating hours: 8 AM to 5 PM
        $operatingStart = 8;
        $operatingEnd = 17;

        // Generate time slots for each day
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateSlots = [];

            for ($hour = $operatingStart; $hour < $operatingEnd; $hour++) {
                $startTime = sprintf('%02d:00', $hour);
                $endTime = sprintf('%02d:00', $hour + 1);

                // Check if slot is blocked
                $isBlocked = BlockedTimeSlot::where('room_id', $room->id)
                    ->where('blocked_date', $currentDate->format('Y-m-d'))
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where(function ($q) use ($startTime) {
                            $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>', $startTime);
                        })->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '>=', $startTime)
                                ->where('end_time', '<=', $endTime);
                        });
                    })
                    ->exists();

                if ($isBlocked) {
                    $status = 'blocked';
                } else {
                    // Check existing reservations
                    $reservation = RoomReservation::where('room_id', $room->id)
                        ->where('reservation_date', $currentDate->format('Y-m-d'))
                        ->where(function ($query) use ($startTime, $endTime) {
                            $query->where(function ($q) use ($startTime) {
                                $q->where('start_time', '<=', $startTime)
                                    ->where('end_time', '>', $startTime);
                            })->orWhere(function ($q) use ($startTime, $endTime) {
                                $q->where('start_time', '>=', $startTime)
                                    ->where('end_time', '<=', $endTime);
                            });
                        })
                        ->first();

                    if ($reservation) {
                        $status = $reservation->status === 'approved' ? 'reserved' : 'pending';
                    } else {
                        $status = 'available';
                    }
                }

                $dateSlots[] = [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $status,
                ];
            }

            $slots[$currentDate->format('Y-m-d')] = $dateSlots;
            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'room' => $room,
            'slots' => $slots,
        ]);
    }

    /**
     * Create a new reservation
     */
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'barcode' => 'required|string',
                'reservation_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'purpose' => 'required|string|max:500',
                'participant_count' => 'nullable|integer|min:0|max:40',
                'participant_names' => 'nullable|array|max:40',
                'participant_names.*' => 'required|string|max:255',
                'participant_ids' => 'nullable|array|max:40',
                'participant_ids.*' => 'nullable|string|max:100',
            ]);

            // Check if user exists by barcode
            $user = User::where('barcode', $validated['barcode'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student ID not found. Please check your student/school ID.',
                ]);
            }

            $room = Room::findOrFail($validated['room_id']);

            // Check if slot is available
            if (!$room->isAvailableAt($validated['reservation_date'], $validated['start_time'], $validated['end_time'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot is no longer available. Please select another time.',
                ]);
            }

            // Create reservation
            $reservation = RoomReservation::create([
                'room_id' => $validated['room_id'],
                'user_id' => $user->id,
                'reservation_date' => $validated['reservation_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'purpose' => $validated['purpose'],
                'status' => 'pending',
                'participant_count' => $validated['participant_count'] ?? 0,
                'participant_names' => $validated['participant_names'] ?? [],
                'participant_ids' => $validated['participant_ids'] ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservation submitted successfully! Your reservation is pending approval.',
                'reservation' => $reservation->load('room', 'user'),
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $firstError = reset($errors);
            $message = is_array($firstError) ? $firstError[0] : $firstError;

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ]);
        }
    }
}
