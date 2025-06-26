<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    protected $fillable = [
        'name',
        'referral_code',
        'ukm_id',
        'description'
    ];

    /**
     * The users that belong to the group.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot(['is_muted', 'created_at', 'updated_at'])
            ->withTimestamps();
    }

    /**
     * Get the chats for the group.
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
    
    /**
     * Get the UKM that owns the group.
     */
    public function ukm(): BelongsTo
    {
        return $this->belongsTo(UKM::class);
    }
}
