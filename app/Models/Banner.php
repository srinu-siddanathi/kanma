<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    // Banner sections
    const SECTION_WEB_HOME = 'web_home';
    const SECTION_MOBILE_APP_MAIN = 'mobile_app_main';
    const SECTION_MOBILE_APP_BOTTOM = 'mobile_app_bottom';

    protected $fillable = [
        'section',
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
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    public static function getSections()
    {
        return [
            self::SECTION_WEB_HOME => 'Web Home Page Slider',
            self::SECTION_MOBILE_APP_MAIN => 'Mobile App Main Banner',
            self::SECTION_MOBILE_APP_BOTTOM => 'Mobile App Bottom Banner'
        ];
    }
} 