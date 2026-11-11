<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Models\ClientRequest;
use App\Models\User;

class ClientRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ClientRequest $request): bool
    {
        return $user->isManager() || $request->client_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isClient();
    }

    public function cancel(User $user, ClientRequest $request): bool
    {
        if ($user->isClient()) {
            return $request->client_id === $user->id
                && $request->status === RequestStatus::NEW;
        }
        if ($user->isManager()) {
            return in_array($request->status, [RequestStatus::NEW, RequestStatus::IN_PROGRESS], true);
        }
        return false;
    }

    public function manage(User $user): bool
    {
        return $user->isManager();
    }
}
