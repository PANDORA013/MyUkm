<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use App\Models\UKM;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Group;

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
        'email',
        'nim',
        'password',
        'photo',
        'role',
        'last_seen_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    // Groups relationship
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user');
    }

    // Chats relationship
    // Original password relation (admin only)
    public function originalPassword(): HasOne
    {
        return $this->hasOne(UserPassword::class);
    }

    public function getPlainPasswordAttribute(): ?string
    {
        if (!$this->relationLoaded('originalPassword')) {
            $this->load('originalPassword');
        }
        $record = $this->originalPassword;
        return $record ? Crypt::decryptString($record->password_enc) : null;
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function ukm(): BelongsToMany
    {
        return $this->belongsToMany(UKM::class, 'group_user', 'user_id', 'group_id')
            ->withPivot('is_muted');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
