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

Broadcast::channel('kendaraan.{id}', function ($user) {
    return $user !== null;
});


// Broadcast::channel('kendaraan.{id}', function ($user, $id) {
//     return (int) $user->id === $id; // Atau autentikasi lain yang sesuai
// });


