<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $project->name }}</title>
</head>

<body>
    <h1>{{ $project->name }}</h1>
    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif

    @if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    @can('update', $project)
    <h2>Project members</h2>

    <form method="POST"
        action="{{ route('projects.members.update', $project) }}">
        @csrf
        @method('PUT')

        @foreach ($users as $user)
        <label>
            <input type="checkbox"
                name="members[]"
                value="{{ $user->id }}"
                @checked(in_array(
                $user->id,
            old('members', $errors->any() ? [] : $project->users->modelKeys())
            ))>

            {{ $user->name }} ({{ $user->role }})
        </label>
        <br>
        @endforeach

        <button type="submit">Save members</button>
    </form>
    @endcan
    <h2>Tickets</h2>

    @can('create', [\App\Models\Ticket::class, $project])
    <a href="{{ route('tickets.create', $project) }}">New ticket</a>
    @endcan

    <table border="1" cellpadding="6">
        <tr>
            <th>Title</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Assignee</th>
            <th>Due</th>
            <th>Actions</th>
        </tr>
        @forelse ($project->tickets as $ticket)
        <tr>
            <td><a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->title }}</a></td>
            <td>{{ $ticket->status->label() }}</td>
            <td>{{ $ticket->priority->label() }}</td>
            <td>{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>
            <td>{{ $ticket->due_date->format('Y-m-d') }}</td>
            <<td>
                @can('update', $ticket)
                <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>

                @can('delete', $ticket)
                <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" style="display:inline"
                    onsubmit="return confirm('Delete this ticket?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
                @endcan
                @elsecan('updateStatus', $ticket)
                <form method="POST" action="{{ route('tickets.status.update', $ticket) }}">
                    @csrf
                    @method('PATCH')
                    <select name="status">
                        @foreach (\App\Enums\TicketStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($ticket->status === $status)>
                            {{ $status->label() }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit">Update</button>
                </form>
                @endcan
                </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">No tickets yet.</td>
        </tr>
        @endforelse
    </table>

    <a href="{{ route('projects.index') }}">Back to projects</a>
</body>

</html>