<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = [
        'user_id',
        'library_section',
        'login_time',
        'logout_time'
    ];

}