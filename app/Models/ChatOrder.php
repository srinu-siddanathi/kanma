<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatOrder extends Model
{
    protected $fillable = [
        'user_id',
        'shop_id',
        'name',
        'status',
        'total_amount',
        'notes',
        'scheduled_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class)->withDefault();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }
} 