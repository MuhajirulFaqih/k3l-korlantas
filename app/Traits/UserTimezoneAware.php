<?php


namespace App\Traits;


use Carbon\Carbon;

trait UserTimezoneAware
{
    protected function asDateTime($value){
        $timezone = auth()->check() ? auth()->user()->timezone : config('app.timezone');

        if ($value instanceof Carbon){
            return new Carbon(
                $value->format('Y-m-d H:i:s.u', $timezone)
            );
        }

        if (is_numeric($value)){
            return Carbon::createFromTimestamp($value)->timezone($timezone);
        }

        if ($this->isStandardDateFormat($value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay()->timezone($timezone);
        }

        return Carbon::createFromFormat(
            str_replace('.v', '.u', $this->getDateFormat()), $value
        )->timezone($timezone);
    }
}
