<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeletion extends Model
{
    protected $table = 'user_deletions';
    
    protected $fillable = [
        'deleted_user_id',
        'deleted_user_name', 
        'deleted_user_nim',
        'deleted_user_email',
        'deleted_user_role',
        'deletion_reason',
        'deleted_by',
        'deletion_notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke user yang menghapus
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
