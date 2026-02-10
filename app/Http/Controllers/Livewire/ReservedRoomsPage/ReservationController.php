<?php

namespace App\Http\Controllers\Livewire\ReservedRoomsPage;

use App\Http\Controllers\Controller;
use App\Models\BlockedTimeSlot;
use App\Models\RoomReservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    /**
     * Get calendar data for all reservations
     */
    public function getCalendarData(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $query = RoomReservation::with(['room', 'user'])
            ->whereBetween('reservation_date', [$validated['start_date'], $validated['end_date']]);

        if (isset($validated['room_id'])) {
            $query->where('room_id', $validated['room_id']);
        }

        $reservations = $query->get();

        // Get blocked time slots
        $blockedQuery = BlockedTimeSlot::with('room')
            ->whereBetween('blocked_date', [$validated['start_date'], $validated['end_date']]);

        if (isset($validated['room_id'])) {
            $blockedQuery->where('room_id', $validated['room_id']);
        }

        $blocked = $blockedQuery->get();

        // Transform to plain arrays with formatted dates to avoid timezone issues
        $formattedReservations = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'room_id' => $reservation->room_id,
                'name' => $userFullName = $reservation->user ? ($reservation->user->fname.' '.$reservation->user->lname) : 'Unknown User',
                'user_id' => $reservation->user_id,
                'reservation_date' => Carbon::parse($reservation->reservation_date)->format('Y-m-d'),
                'start_time' => $reservation->start_time,
                'end_time' => $reservation->end_time,
                'purpose' => $reservation->purpose,
                'status' => $reservation->status,
                'participant_count' => $reservation->participant_count,
                'participant_names' => $reservation->participant_names, // Already array due to cast
                'participant_ids' => $reservation->participant_ids, // Already array due to cast
                'created_at' => $reservation->created_at,
                'updated_at' => $reservation->updated_at,
                'room' => $reservation->room,
                'user' => $reservation->user,
            ];
        });

        $formattedBlocked = $blocked->map(function ($slot) {
            return [
                'id' => $slot->id,
                'room_id' => $slot->room_id,
                'blocked_date' => Carbon::parse($slot->blocked_date)->format('Y-m-d'),
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'reason' => $slot->reason,
                'created_at' => $slot->created_at,
                'updated_at' => $slot->updated_at,
                'room' => $slot->room,
            ];
        });

        return response()->json([
            'success' => true,
            'reservations' => $formattedReservations,
            'blocked_slots' => $formattedBlocked,
        ]);
    }

    /**
     * Get single reservation details
     */
    public function getReservation($id)
    {
        $reservation = RoomReservation::with(['room', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'reservation' => $reservation,
        ]);
    }

    /**
     * Update reservation (approve, cancel, or edit details)
     */
    public function updateReservation(Request $request, $id)
    {
        try {
            $reservation = RoomReservation::findOrFail($id);

            $validated = $request->validate([
                'status' => 'nullable|in:pending,approved,cancelled',
                'reservation_date' => 'nullable|date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
                'purpose' => 'nullable|string|max:500',
                'participant_count' => 'nullable|integer|min:0|max:40',
                'participant_names' => 'nullable|array|max:40',
                'participant_names.*' => 'required|string|max:255',
                'participant_ids' => 'nullable|array|max:40',
                'participant_ids.*' => 'nullable|string|max:100',
            ]);

            // Update fields if provided
            if (isset($validated['status'])) {
                $reservation->status = $validated['status'];
            }

            if (isset($validated['reservation_date'])) {
                $reservation->reservation_date = $validated['reservation_date'];
            }

            if (isset($validated['start_time'])) {
                $reservation->start_time = $validated['start_time'];
            }

            if (isset($validated['end_time'])) {
                $reservation->end_time = $validated['end_time'];
            }

            if (isset($validated['purpose'])) {
                $reservation->purpose = $validated['purpose'];
            }

            // Update participant data
            if (isset($validated['participant_count'])) {
                $reservation->participant_count = $validated['participant_count'];
            }

            if (isset($validated['participant_names'])) {
                $reservation->participant_names = $validated['participant_names'];
            }

            if (isset($validated['participant_ids'])) {
                $reservation->participant_ids = $validated['participant_ids'];
            }

            $reservation->save();

            return response()->json([
                'success' => true,
                'message' => 'Reservation updated successfully',
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
            ], 422);
        }
    }

    /**
     * Delete a reservation
     */
    public function deleteReservation($id)
    {
        $reservation = RoomReservation::findOrFail($id);
        $reservation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reservation deleted successfully',
        ]);
    }

    /**
     * Block a time slot
     */
    public function blockTimeSlot(Request $request)
    {
        try {
            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'blocked_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'reason' => 'nullable|string|max:500',
            ]);

            $blockedSlot = BlockedTimeSlot::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Time slot blocked successfully',
                'blocked_slot' => $blockedSlot->load('room'),
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $firstError = reset($errors);
            $message = is_array($firstError) ? $firstError[0] : $firstError;

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], 422);
        }
    }

    /**
     * Unblock a time slot
     */
    public function unblockTimeSlot(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:blocked_time_slots,id',
        ]);

        $blockedSlot = BlockedTimeSlot::findOrFail($validated['id']);
        $blockedSlot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Time slot unblocked successfully',
        ]);
    }
}
