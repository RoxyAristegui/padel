<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Partido;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartidoPolicy
{
  use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Partido $partido): bool
    {
        return $user->belongsToTeam($partido->team);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin($user->currentTeam);
    }
}
