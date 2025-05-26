<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image_url',
        'button_text',
        'button_url',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
} 