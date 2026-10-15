<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectMembersRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;

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
        

        $projects = $projectService->listFor($request->user());

        return view('projects.index', compact('projects'));
    }
    public function create()
    {

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

        return view('projects.edit', compact('project'));
    }
    public function destroy(Project $project, ProjectService $projectService)
    {

        $projectService->delete($project);

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function show(Project $project, ProjectService $projectService)
{
    return view('projects.show', [
        'project' => $projectService->loadDetails($project),
        'candidates' => $projectService->memberCandidates(),
    ]);
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
