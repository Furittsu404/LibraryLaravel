<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

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
        'created_at',
        'updated_at'
    ];
}