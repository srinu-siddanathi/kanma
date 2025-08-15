<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    const TYPE_TEXT = 'text';
    const TYPE_VOICE = 'voice';
    const TYPE_IMAGE = 'image';
    const TYPE_SCHEDULE = 'schedule';

    protected $fillable = [
        'chat_order_id',
        'sender_id',
        'type',
        'content',
        'media_path',
        'duration',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'duration' => 'integer'
    ];

    public function chatOrder(): BelongsTo
    {
        return $this->belongsTo(ChatOrder::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id')->withDefault([
            'name' => 'Unknown User'
        ]);
    }

    public function getMediaUrlAttribute()
    {
        if (!$this->media_path) {
            return null;
        }
        return url($this->media_path);
    }
} 