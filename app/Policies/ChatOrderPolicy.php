<?php

namespace App\Policies;

use App\Models\ChatOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatOrderPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ChatOrder $chatOrder)
    {
        return $user->id === $chatOrder->user_id || $user->shop->id === $chatOrder->shop_id;
    }

    public function create(User $user)
    {
        return true; // Any authenticated user can create a chat order
    }

    public function update(User $user, ChatOrder $chatOrder)
    {
        return $user->id === $chatOrder->user_id || $user->shop->id === $chatOrder->shop_id;
    }

    public function delete(User $user, ChatOrder $chatOrder)
    {
        return $user->id === $chatOrder->user_id || $user->shop->id === $chatOrder->shop_id;
    }
} 