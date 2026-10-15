<x-layout title="Projects">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Projects</h1>

        @can('create', \App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="btn btn-primary">Create Project</a>
        @endcan
    </div>

    <div class="list-group">
        @forelse ($projects as $project)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ route('projects.show', $project) }}" class="fw-semibold text-decoration-none">
                    {{ $project->name }}
                </a>

                <div class="d-flex gap-2">
                    @can('update', $project)
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    @endcan

                    @can('delete', $project)
                        <form method="POST" action="{{ route('projects.destroy', $project) }}"
                              onsubmit="return confirm('Delete this project?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <div class="list-group-item text-muted">No projects yet.</div>
        @endforelse
    </div>
</x-layout>