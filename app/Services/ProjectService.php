<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public function create(array $data, User $creator)
    {
        $project = new Project;
        $project->name = $data['name'];
        $project->created_by = $creator->id;
        $project->save();

        return $project;
    }

    public function listFor(User $user)
    {
        if ($user->role === UserRole::Admin) {
            return Project::all();
        }

        return $user->projects;
    }

    public function update(Project $project, array $data): Project
    {
        $project->name = $data['name'];
        $project->save();

        return $project;
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }

    public function updateMembers(Project $project, array $members): void
    {
        $removedIds = $project->users()
            ->whereNotIn('users.id', $members)
            ->pluck('users.id');

        $project->users()->sync($members);

        Ticket::where('project_id', $project->id)
            ->whereIn('assignee_id', $removedIds)
            ->update(['assignee_id' => null]);
    }

    public function memberCandidates(): Collection
    {
        return User::whereIn('role', ['manager', 'user'])->orderBy('name')->get();
    }

    public function loadDetails(Project $project): Project
    {
        return $project->load(['users', 'tickets.assignee']);
    }
}
