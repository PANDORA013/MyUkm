<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'last_activity_at',
        'ip_address',
        'user_agent',
        'device',
        'platform',
        'browser',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
