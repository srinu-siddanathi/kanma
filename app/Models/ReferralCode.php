<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'uses',
        'reward_amount',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reward_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 