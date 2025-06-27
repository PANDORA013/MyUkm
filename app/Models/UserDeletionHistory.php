<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeletionHistory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'nim',
        'email',
        'role',
        'deletion_reason',
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
