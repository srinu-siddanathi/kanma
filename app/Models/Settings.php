<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description'
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]
        );
        return $setting;
    }

    public static function getStoreLocation()
    {
        return [
            'latitude' => (float) static::get('store_latitude', 0),
            'longitude' => (float) static::get('store_longitude', 0),
            'service_radius' => (float) static::get('service_radius', 5.00),
            'delivery_charge' => (float) static::get('delivery_charge', 0.00),
            'min_order_amount' => (float) static::get('min_order_amount', 0.00)
        ];
    }
} 