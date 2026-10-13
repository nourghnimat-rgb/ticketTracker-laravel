<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectMembersRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Gate;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function store(StoreProjectRequest $request, ProjectService $projectService)
    {
        $projectService->create($request->validated(), $request->user());

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }
    public function index(Request $request, ProjectService $projectService)
    {
        Gate::authorize('viewAny', Project::class);

        $projects = $projectService->listFor($request->user());

        return view('projects.index', compact('projects'));
    }
    public function create()
    {
        Gate::authorize('create', Project::class);

        return view('projects.create');
    }
    public function update(
        UpdateProjectRequest $request,
        Project $project,
        ProjectService $projectService
    ) {
        $projectService->update($project, $request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }
    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }
    public function destroy(Project $project, ProjectService $projectService)
    {
        Gate::authorize('delete', $project);

        $projectService->delete($project);

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $users = collect();

        if (Gate::allows('update', $project)) {
            $users = User::all();
        }
        $project->load('tickets.assignee');
        return view('projects.show', compact('project', 'users'));
    }
    public function updateMembers(
        UpdateProjectMembersRequest $request,
        Project $project,
        ProjectService $projectService
    ) {
        $data = $request->validated();

        $projectService->updateMembers($project, $data['members'] ?? []);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project members updated successfully.');
    }
}
