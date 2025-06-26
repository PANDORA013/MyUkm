<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Chat extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'group_id', 'message', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    protected $appends = ['is_read'];

    // Relasi ke User (pengirim pesan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Group (grup chat)
    public function group()
    {
        return $this->belongsTo(Group::class);
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
