<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections';

    protected $fillable = [
        'code',
        'name',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get only active sections
     */
    public static function active()
    {
        return self::where('is_active', true)->get();
    }
}
