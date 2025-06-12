<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'referral_code'
    ];

    // Users relationship (many-to-many)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    // Chats relationship (one-to-many)
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
