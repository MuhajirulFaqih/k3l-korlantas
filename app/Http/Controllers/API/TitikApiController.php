<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
use App\Http\Controllers\Controller;

class TitikApiController extends Controller
{
    public function index(Request $request)
    {
        $client = new Guzzle(['http_errors' => false]);
        $url  = env('URL_LAPAN', 'http://103.51.131.166/getHS?class=hotspot&conf_lvl=low&enddate=&id=0&loc={"stt":"Indonesia","disp":"Indonesia"}&mode=cluster&name=Hotspot&startdate=&time=last24h&visibility=true');
        try {
            $response = $client->request('GET', $url);
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            $hotspot = [];
            $tinggi = 0;
            $sedang = 0;
            $rendah = 0;
            foreach ($data['features'] as $k => $v) {
                if($v['properties']['c'] == 7) {
                    $rendah++;
                } elseif($v['properties']['c'] == 8) {
                    $sedang++;
                } else {
                    $tinggi++;
                }
                $hotspot['data'][] = [
                    'type' => 'hotspot',
                    'tk' => $v['properties']['c'],
                    'lat' => $v['geometry']['coordinates'][0],
                    'lng' => $v['geometry']['coordinates'][1],
                    'satellite' => $v['properties']['s'],
                    'sumber' => 'Stasiun Bumi LAPAN'
                ];
            }
            $hotspot['total']['tinggi'] = $tinggi;
            $hotspot['total']['sedang'] = $sedang;
            $hotspot['total']['rendah'] = $rendah;
            $hotspot['total']['semua'] = $tinggi + $sedang + $rendah;
            return response()->json($hotspot, 200);
        }
        catch (BadResponseException $e) {
            return response()->json(['message' => 'Terjadi kesalahan ' . $e->getMessage()], 500);
        }
    }
}