<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Un usuario solo puede escuchar en el canal 'agente' con su propio ID.
Broadcast::channel('agente.{agentId}', function ($user, $agentId) {
    return (int) $user->id === (int) $agentId;
});