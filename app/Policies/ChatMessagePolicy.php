<?php

namespace App\Policies;

use App\Models\ChatMessage;
use App\Models\ChatOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatMessagePolicy
{
    use HandlesAuthorization;

    public function view(User $user, ChatOrder $chatOrder)
    {
        return $user->id === $chatOrder->user_id || $user->shop->id === $chatOrder->shop_id;
    }

    public function create(User $user, ChatOrder $chatOrder)
    {
        return $user->id === $chatOrder->user_id || $user->shop->id === $chatOrder->shop_id;
    }

    public function update(User $user, ChatMessage $message)
    {
        return $user->id === $message->sender_id || $user->shop->id === $message->chatOrder->shop_id;
    }

    public function delete(User $user, ChatMessage $message)
    {
        return $user->id === $message->sender_id || $user->shop->id === $message->chatOrder->shop_id;
    }
} 