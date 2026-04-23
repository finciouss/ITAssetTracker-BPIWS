<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'user_email',
        'action_type',
        'description',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];
}
