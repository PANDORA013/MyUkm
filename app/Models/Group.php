<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Chat;
use App\Models\User;
use App\Models\UKM;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'referral_code',
        'description',
        'created_by',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The users that belong to the group.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->using(GroupUser::class)
            ->withPivot([
                'is_muted',
                'is_admin',
                'created_at',
                'updated_at',
                'deleted_at'
            ])
            ->withTimestamps()
            ->withTrashed()
            ->withTimestamps()
            ->withTrashed();
    }

    /**
     * Get the chats for the group.
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class)->withTrashed();
    }
    
    /**
     * Get the admin users of the group.
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->wherePivot('is_admin', true)
            ->withTimestamps();
    }
    
    /**
     * Get the creator of the group.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the UKM that owns the group.
     */
    public function ukm(): BelongsTo
    {
        return $this->belongsTo(UKM::class)->withTrashed();
    }
    
    /**
     * Scope a query to only include active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'referral_code';
    }
}
