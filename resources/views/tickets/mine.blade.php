<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>My Tickets</h1>

<table border="1" cellpadding="6">
    <tr>
        <th>Title</th>
        <th>Project</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Assignee</th>
        <th>Due</th>
    </tr>
    @forelse ($tickets as $ticket)
        <tr>
            <td>{{ $ticket->title }}</td>
            <td><a href="{{ route('projects.show', $ticket->project) }}">{{ $ticket->project->name }}</a></td>
            <td>{{ $ticket->status->label() }}</td>
            <td>{{ $ticket->priority->label() }}</td>
            <td>{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>
            <td>{{ $ticket->due_date->format('Y-m-d') }}</td>
        </tr>
    @empty
        <tr><td colspan="6">No tickets.</td></tr>
    @endforelse
</table>

<a href="{{ route('projects.index') }}">Back to projects</a>
</body>
</html>