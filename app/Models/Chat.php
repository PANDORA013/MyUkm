<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Chat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'group_id',
        'message',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['is_read'];

    /**
     * Get the user that owns the chat message.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the group that the chat message belongs to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class)->withTrashed();
    }

    /**
     * The users that are participants in the chat.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'chat_user')
            ->withTimestamps()
            ->withPivot([
                'last_read_at',
                'deleted_at'
            ])
            ->withTrashed();
    }
    
    // Relasi ke pesan-pesan dalam chat
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Get whether the message has been read
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    // Mark message as read
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => Carbon::now()]);
            broadcast(new \App\Events\MessageRead($this))->toOthers();
        }
    }

    // Scope for unread messages
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Scope for messages in a specific group
    public function scopeInGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }
}
