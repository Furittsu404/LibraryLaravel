<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'capacity' => 'integer'
    ];

    /**
     * Get all reservations for this room
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(RoomReservation::class);
    }

    /**
     * Get blocked time slots for this room
     */
    public function blockedTimeSlots(): HasMany
    {
        return $this->hasMany(BlockedTimeSlot::class);
    }

    /**
     * Check if room is available for a specific date and time
     */
    public function isAvailableAt($date, $startTime, $endTime): bool
    {
        // Check if room is generally available
        if (!$this->is_available) {
            return false;
        }

        // Check for blocked time slots
        $isBlocked = $this->blockedTimeSlots()
            ->where('blocked_date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->exists();

        if ($isBlocked) {
            return false;
        }

        // Check for existing reservations
        $hasReservation = $this->reservations()
            ->where('reservation_date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->exists();

        return !$hasReservation;
    }
}
