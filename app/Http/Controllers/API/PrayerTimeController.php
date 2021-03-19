<?php

namespace App\Http\Controllers\API;

use Athasamid\LatLngToTimezone\LatLngToTimezone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use IslamicNetwork\PrayerTimes\Method;
use IslamicNetwork\PrayerTimes\PrayerTimes;

class PrayerTimeController extends Controller
{
    function getToday(Request $request){
        $lat = $request->lat ?? env('DEFAULT_LAT');
        $lng = $request->lng ?? env('DEFAULT_LNG');

        $timezone = LatLngToTimezone::latLngToTimezoneString($lat, $lng);

        $pt = new PrayerTimes(Method::METHOD_EGYPT);
        $times = $pt->getTimesForToday($lat, $lng, $timezone, null, PrayerTimes::LATITUDE_ADJUSTMENT_METHOD_ANGLE, null, PrayerTimes::TIME_FORMAT_24H);

        $times['TengahMalam'] = $times['Midnight'];
        unset($times['Midnight']);

        return response()->json(['data' => $times, 'timezone' => ['name' => $timezone, 'utc_offset' => LatLngToTimezone::latLngToUTCOffset($lat, $lng)]]);
    }
}
