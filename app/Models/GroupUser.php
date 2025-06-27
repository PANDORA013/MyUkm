<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupUser extends Pivot
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'group_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'group_id',
        'is_muted',
        'is_admin',
        'joined_at',
        'left_at',
        'deleted_at'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_muted' => 'boolean',
        'is_admin' => 'boolean',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dateFormat = 'Y-m-d H:i:s';
    
    /**
     * Get the user that owns the group membership.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    
    /**
     * Get the group that owns the group membership.
     */
    public function group()
    {
        return $this->belongsTo(Group::class)->withTrashed();
    }
}
