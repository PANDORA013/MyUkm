<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $nim
 * @property string $password
 * @property string|null $photo
 * @property-read Collection|Group[] $groups
 * @property-read Collection|Chat[] $chats
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'nim',
        'password',
        'photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    // Groups relationship
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user')
                    ->withTimestamps();
    }

    // Chats relationship
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
