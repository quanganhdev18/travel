<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = Conversation::find($id);
    if (! $conversation) {
        return false;
    }

    // Allow if user is the customer OR if user has 'cskh' or 'admin' role
    return $user->id === $conversation->user_id || $user->hasAnyRole(['cskh', 'Admin', 'Super Admin', 'Staff']);
});

Broadcast::channel('admin.chat', function ($user) {
    return $user->hasAnyRole(['Super Admin', 'Admin', 'cskh', 'Staff']);
});
