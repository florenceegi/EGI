<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to determine if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('price.{egiId}', function ($egiId, $user = null) {
    return true; // public per guest
    // per private: return $user !== null;
});

Broadcast::channel('user-welcome.{userId}', function ($user, $userId) {
    // L'utente può ascoltare solo il proprio canale
    return $user && (int) $user->id === (int) $userId;
});

// ✅ ADDED: Standard Channel for Private Notifications
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});