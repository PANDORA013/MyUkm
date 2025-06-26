<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UKM;
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
        'password_plain',
        'photo',
        'role',
        'last_seen_at'
    ];
    
    protected $appends = ['plain_password'];
    
    /**
     * Get the plain text password (only for admin website viewing)
     *
     * @return string|null
     */
    public function getPlainPasswordAttribute()
    {
        try {
            // Hanya kembalikan password plain jika user yang login adalah admin website
            $authUser = AuthFacade::user();
            if ($authUser && $authUser->role === 'admin_website') {
                return $this->attributes['password_plain'] ?? 'Password dienkripsi';
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error in getPlainPasswordAttribute: ' . $e->getMessage());
            return null;
        }
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * The groups that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->withPivot(['is_muted', 'created_at', 'updated_at'])
            ->withTimestamps();
    }

    /**
     * The UKMs that the user belongs to through groups.
     */
    public function ukms(): BelongsToMany
    {
        return $this->belongsToMany(UKM::class, 'group_user', 'user_id', 'group_id')
            ->withPivot(['is_muted', 'created_at', 'updated_at'])
            ->withTimestamps();
    }

    /**
     * Get the user's chats.
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Get the user's role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the URL for the user's profile photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
