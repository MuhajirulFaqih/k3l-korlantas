<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel(env('SOCKET_PREFIX').':Monit', function ($user){
    return in_array($user->jenis_pemilik, ['admin', 'kesatuan']);
});

Broadcast::channel(env('SOCKET_PREFIX').':Monit.{id}', function ($user, $id) {
    return in_array($user->jenis_pemilik, ['admin', 'kesatuan']) && $user->id === (int) $id;
});

Broadcast::channel(env('SOCKET_PREFIX').':Personil.{id}', function ($user, $id) {
    return in_array($user->jenis_pemilik, ['personil']) && $user->id_pemilik === (int) $id;
});

Broadcast::channel('Video.Call', function($user){
    return in_array($user->jenis_pemilik, ['admin', 'personil', 'bhabin']);
});

Broadcast::channel(env('SOCKET_PREFIX').'-online', function ($user){
    return $user;
});
