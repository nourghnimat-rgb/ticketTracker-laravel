<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    public function create(array $data, User $creator)
    {
        $project = new Project();
        $project->name = $data['name'];
        $project->created_by = $creator->id;
        $project->save();

        return $project;
    }
    public function listFor(User $user)
{
    if ($user->role === 'admin') {
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
    $project->users()->sync($members);
}
public function memberCandidates(): Collection
{
    return User::whereIn('role', ['manager', 'user'])->orderBy('name')->get();
}
}