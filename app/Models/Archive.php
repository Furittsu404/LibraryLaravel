<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $table = 'users_archive';

    protected $fillable = [
        'id',
        'lname',
        'fname',
        'mname',
        'address',
        'email',
        'sex',
        'course',
        'section',
        'phonenumber',
        'barcode',
        'user_status',
        'user_type',
        'account_status',
        'expiration_date',
        'archived_at',
        'created_at',
        'updated_at'
    ];
}