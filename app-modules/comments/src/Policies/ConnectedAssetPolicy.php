<?php

namespace Hotash\Comments\Policies;

use App\Models\User;
use Hotash\Comments\Models\ConnectedAsset;

class ConnectedAssetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ConnectedAsset $asset): bool
    {
        return $asset->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ConnectedAsset $asset): bool
    {
        return $asset->user_id === $user->id;
    }

    public function delete(User $user, ConnectedAsset $asset): bool
    {
        return $asset->user_id === $user->id;
    }
}
