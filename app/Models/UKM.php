<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UKM extends Model
{
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
        'nama',
        'kode',
        'logo',
        'kategori',
        'deskripsi',
        'status'
    ];

    /**
     * Get the groups that belong to this UKM.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * The users that belong to the UKM through groups.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
            ->using(GroupUser::class)
            ->withPivot(['is_muted', 'created_at', 'updated_at'])
            ->withTimestamps();
    }
}
