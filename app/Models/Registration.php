<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'ukm_id',
        'status',
        'data', // JSON data tambahan
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'data' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ukm()
    {
        return $this->belongsTo(UKM::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
