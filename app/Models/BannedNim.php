<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannedNim extends Model
{
    protected $table = 'banned_nims';
    protected $fillable = [
        'nim',
        'banned_by',
        'reason',
    ];

    /**
     * Relasi ke user yang melakukan ban
     */
    public function bannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }
}
