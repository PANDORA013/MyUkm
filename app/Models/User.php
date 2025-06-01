<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $nim
 * @property string $password
 * @property-read Collection|Group[] $groups
 * @property-read Collection|Chat[] $chats
 * @method BelongsToMany groups()
 * @method HasMany chats()
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'nim',
        'password'
    ];

    protected $hidden = [
        'password',
    ];

    // Groups relationship
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user');
    }

    // Chats relationship
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
