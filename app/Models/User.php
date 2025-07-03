<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\UserPassword;
use App\Models\Registration;
use App\Models\UserActivity;
use App\Models\Message;
use App\Models\Chat;
use App\Models\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UKM;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    use Notifiable, SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'nim',
        'email',
        'password',
        'password_plain',
        'photo',
        'role',
        'ukm_id',
        'last_seen_at'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
        'password_plain'
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'password' => 'hashed'
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

    /**
     * The UKM that the user belongs to.
     */
    public function ukm(): BelongsTo
    {
        return $this->belongsTo(UKM::class);
    }

    /**
     * The groups that the user belongs to.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id')
            ->using(GroupUser::class)
            ->withPivot([
                'is_muted',
                'is_admin',
                'created_at',
                'updated_at',
                'deleted_at'
            ])
            ->withTimestamps()
            ->withTrashed();
    }

    /**
     * The groups where the user is an admin.
     */
    public function adminGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id')
            ->using(GroupUser::class)
            ->wherePivot('is_admin', true)
            ->withPivot([
                'is_muted',
                'is_admin',
                'created_at',
                'updated_at',
                'deleted_at'
            ])
            ->withTimestamps()
            ->withTrashed();
    }

    /**
     * The UKMs that the user belongs to through groups.
     */
    public function ukms(): BelongsToMany
    {
        return $this->belongsToMany(UKM::class, 'group_user', 'user_id', 'group_id')
            ->using(GroupUser::class)
            ->withPivot([
                'is_muted',
                'is_admin',
                'created_at',
                'updated_at',
                'deleted_at'
            ])
            ->withTimestamps()
            ->withTrashed();
    }

    /**
     * Get the user's password encryption record.
     */
    public function passwordEncrypted(): HasOne
    {
        return $this->hasOne(UserPassword::class);
    }

    /**
     * Get the user's registrations.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Get the user's last seen record.
     */
    public function lastSeen(): HasOne
    {
        return $this->hasOne(UserActivity::class);
    }

    /**
     * Get the user's sent messages.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the user's received messages.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the user's chats.
     */
    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_user')
            ->withTimestamps()
            ->withTrashed();
    }

    /**
     * Get the user's chat messages.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    /**
     * Get the user's created chats.
     */
    public function createdChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    /**
     * Get the user's role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    
    /**
     * Check if user has a specific role.
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->role->name === $roles;
        }
        
        return in_array($this->role->name, $roles);
    }
    
    /**
     * Check if user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role && in_array($this->role->name, ['admin_website', 'admin_ukm']);
    }
    
    /**
     * Check if user is a regular member.
     *
     * @return bool
     */
    public function isMember(): bool
    {
        return $this->role && $this->role->name === 'member';
    }

    /**
     * Get the URL for the user's profile photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
    
    /**
     * Check if user is admin in a specific group
     *
     * @param int|Group $group
     * @return bool
     */
    public function isAdminInGroup($group): bool
    {
        $groupId = $group instanceof Group ? $group->id : $group;
        
        return $this->groups()
            ->where('group_id', $groupId)
            ->wherePivot('is_admin', true)
            ->exists();
    }
    
    /**
     * Check if user is member (not admin) in a specific group
     *
     * @param int|Group $group
     * @return bool
     */
    public function isMemberInGroup($group): bool
    {
        $groupId = $group instanceof Group ? $group->id : $group;
        
        return $this->groups()
            ->where('group_id', $groupId)
            ->wherePivot('is_admin', false)
            ->exists();
    }
    
    /**
     * Get user's role in a specific group (admin or member)
     *
     * @param int|Group $group
     * @return string|null 'admin', 'member', or null if not in group
     */
    public function getRoleInGroup($group): ?string
    {
        $groupId = $group instanceof Group ? $group->id : $group;
        
        $membership = $this->groups()
            ->where('group_id', $groupId)
            ->first();
            
        if (!$membership) {
            return null;
        }
        
        return $membership->pivot->is_admin ? 'admin' : 'member';
    }
    
    /**
     * Promote user to admin in a specific group
     *
     * @param int|Group $group
     * @return bool
     */
    public function promoteToAdminInGroup($group): bool
    {
        $groupId = $group instanceof Group ? $group->id : $group;
        
        if (!$this->groups()->where('group_id', $groupId)->exists()) {
            return false; // User is not a member of this group
        }
        
        $this->groups()->updateExistingPivot($groupId, ['is_admin' => true]);
        return true;
    }
    
    /**
     * Demote user from admin to member in a specific group
     *
     * @param int|Group $group
     * @return bool
     */
    public function demoteFromAdminInGroup($group): bool
    {
        $groupId = $group instanceof Group ? $group->id : $group;
        
        if (!$this->groups()->where('group_id', $groupId)->exists()) {
            return false; // User is not a member of this group
        }
        
        $this->groups()->updateExistingPivot($groupId, ['is_admin' => false]);
        return true;
    }
}
