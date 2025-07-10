<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UkmDeletion extends Model
{
    protected $table = 'ukm_deletions';
    protected $fillable = [
        'ukm_id',
        'ukm_name',
        'ukm_code',
        'deleted_by',
        'deletion_reason',
    ];

    /**
     * Relasi ke user yang melakukan penghapusan UKM
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
