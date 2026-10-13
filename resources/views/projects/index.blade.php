<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Projects</title>
</head>

<body>
    <a href="{{ route('tickets.mine') }}">My Tickets</a>
    <h1>Projects</h1>

    @can('create', App\Models\Project::class)
    <a href="{{ route('projects.create') }}">Create Project</a>
    @endcan

    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif

    <<ul>
        @forelse ($projects as $project)
        <li>
            <a href="{{ route('projects.show', $project) }}">
                {{ $project->name }}
            </a>

            @can('update', $project)
            <a href="{{ route('projects.edit', $project) }}">Edit</a>
            @endcan

            @can('delete', $project)
            <form method="POST"
                action="{{ route('projects.destroy', $project) }}">
                @csrf
                @method('DELETE')

                <button type="submit">Delete</button>
            </form>
            @endcan
        </li>
        @empty
        <li>No projects yet.</li>
        @endforelse
        </ul>
</body>

</html>