<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedTimeSlot extends Model
{
    protected $fillable = [
        'room_id',
        'blocked_date',
        'start_time',
        'end_time',
        'reason'
    ];

    protected $casts = [
        'blocked_date' => 'date',
        'room_id' => 'integer'
    ];

    /**
     * Get the room for this blocked time slot
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
