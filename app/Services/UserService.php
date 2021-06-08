<?php


namespace App\Services;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function sendWa($nomor, $pesan){
        $token = Cache::remember('token_wa', 14400, function (){
           $response = Http::post(env('WA_URL'). '/api/user/auth', [
               'username' => env('WA_USERNAME'),
               'password' => env('WA_PASSWORD'),
               'client_id' => env('WA_CLIENT_ID'),
               'client_secret' => env('WA_CLIENT_SECRET'),
               'grant_type' => 'password'
           ]);

           if ($response->ok()){
               return $response->json('access_token');
           }
           return null;
        });

        $response = Http::withToken($token)->post(env('WA_URL').'/api/wa/send', [
            'number' => $nomor,
            'text' => $pesan
        ]);

        Log::info("Kirim pesan wa ". $response->body());

        if ($response->status() == 201){
            return $response->json();
        } elseif ($response->status() == 404){
            return ['error' => 'Nomor tidak terdaftar pada whatsapp', 'code' => 404];
        }

        return ['error' => 'Terjadi kesalahan', 'code' => $response->status()];
    }
}
