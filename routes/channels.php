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
    return $user->jenis_pemilik == 'admin';
});

Broadcast::channel(env('SOCKET_PREFIX').':Monit.{id}', function ($user, $id) {
    return $user->jenis_pemilik == 'admin' && $user->id === (int) $id;
});

Broadcast::channel('Video.Call', function($user){
    return in_array($user->jenis_pemilik, ['admin', 'personil', 'bhabin']);
});

Broadcast::channel(env('SOCKET_PREFIX').'-online', function ($user){
    return $user;
});
