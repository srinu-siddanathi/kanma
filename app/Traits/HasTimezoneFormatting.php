<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasTimezoneFormatting
{
    /**
     * Get formatted date in application timezone
     */
    public function getFormattedDate($field, $format = null)
    {
        if (!$this->$field) {
            return null;
        }

        $format = $format ?? config('timezone.web_date_format', 'M d, Y H:i');
        return $this->$field->format($format);
    }

    /**
     * Get formatted date in UTC
     */
    public function getUtcDate($field, $format = 'Y-m-d H:i:s')
    {
        if (!$this->$field) {
            return null;
        }

        return $this->$field->utc()->format($format);
    }

    /**
     * Get formatted date in Asia/Kolkata timezone
     */
    public function getAsiaKolkataDate($field, $format = 'Y-m-d H:i:s')
    {
        if (!$this->$field) {
            return null;
        }

        return $this->$field->setTimezone('Asia/Kolkata')->format($format);
    }

    /**
     * Get formatted date for API responses
     */
    public function getApiDate($field)
    {
        if (!$this->$field) {
            return null;
        }

        $timezone = config('timezone.api_timezone', 'Asia/Kolkata');
        $format = config('timezone.api_date_format', 'Y-m-d H:i:s');
        
        return $this->$field->setTimezone($timezone)->format($format);
    }

    /**
     * Get date with timezone information for debugging
     */
    public function getDateWithTimezone($field)
    {
        if (!$this->$field) {
            return null;
        }

        return [
            'utc' => $this->getUtcDate($field),
            'app_timezone' => $this->getFormattedDate($field, 'Y-m-d H:i:s T'),
            'asia_kolkata' => $this->getAsiaKolkataDate($field),
            'timestamp' => $this->$field->timestamp,
        ];
    }
} 