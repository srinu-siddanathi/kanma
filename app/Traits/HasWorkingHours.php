<?php

namespace App\Traits;

trait HasWorkingHours
{
    public static function getDefaultWorkingHours()
    {
        return [
            'monday' => ['open' => '09:00', 'close' => '21:00'],
            'tuesday' => ['open' => '09:00', 'close' => '21:00'],
            'wednesday' => ['open' => '09:00', 'close' => '21:00'],
            'thursday' => ['open' => '09:00', 'close' => '21:00'],
            'friday' => ['open' => '09:00', 'close' => '21:00'],
            'saturday' => ['open' => '09:00', 'close' => '21:00'],
            'sunday' => ['open' => '10:00', 'close' => '20:00']
        ];
    }
} 