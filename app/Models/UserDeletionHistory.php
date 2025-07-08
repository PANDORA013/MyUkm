<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeletionHistory extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_nim',
        'user_email',
        'user_role',
        'reason',
        'deleted_by'
    ];
    
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Get the admin who deleted the user
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
