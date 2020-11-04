<?php

namespace App\Policies;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParticipantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can process the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Participant  $participant
     * @return mixed
     */
    public function process(User $user, Participant $participant)
    {
        return $user->id === $participant->user_id && $participant->finish_at == null;
    }

    /**
     * Determine whether the user owner the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Participant  $participant
     * @return mixed
     */
    public function owner(User $user, Participant $participant)
    {
        return $user->id === $participant->user_id && $participant->finish_at;
    }
}
