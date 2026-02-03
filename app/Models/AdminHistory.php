<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminHistory extends Model
{
    protected $table = 'admin_history';
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'action',
        'description',
        'date_time'
    ];

    protected $casts = [
        'date_time' => 'datetime'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
