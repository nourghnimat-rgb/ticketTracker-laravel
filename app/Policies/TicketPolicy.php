<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
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
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $ticket->project->users()
            ->whereKey($user->id)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'manager'
            && $project->users()->whereKey($user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'manager'
            && $ticket->project->users()->whereKey($user->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $this->update($user, $ticket);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return false;
    }

    public function updateStatus(User $user, Ticket $ticket): bool
    {
        if ($this->update($user, $ticket)) {
            return true;
        }

        return $user->role === 'user'
            && $ticket->assignee_id === $user->id
            && $ticket->project->users()->whereKey($user->id)->exists();
    }
}
