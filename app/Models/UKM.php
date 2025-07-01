<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Group;
use App\Models\User;
use App\Models\Registration;

class UKM extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ukms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the groups that belong to this UKM.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class)->withTrashed();
    }
    
    /**
     * Get the active groups for the UKM.
     */
    public function activeGroups(): HasMany
    {
        return $this->hasMany(Group::class)->where('is_active', true);
    }
    
    /**
     * Get the creator of the UKM.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the registrations for the UKM.
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * The users that belong to the UKM through groups.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
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
     * Scope a query to only include active UKMs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'kode';
    }
    
    /**
     * Get the URL to the UKM's logo.
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return asset('images/default-ukm-logo.png');
        }
        
        return asset('storage/' . $this->logo);
    }
}
