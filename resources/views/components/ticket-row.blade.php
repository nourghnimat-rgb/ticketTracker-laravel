@props(['ticket', 'showProject' => false])

<tr>
    <td>
        <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">{{ $ticket->title }}</a>
    </td>
    @if ($showProject)
    <td>
        <a href="{{ route('projects.show', $ticket->project) }}" class="text-decoration-none">{{ $ticket->project->name }}</a>
    </td>
    @endif
    <td>{{ $ticket->category->label() }}</td>
    <td><x-status-badge :status="$ticket->status" /></td>
    <td>{{ $ticket->priority->label() }}</td>
    <td>{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>
    <td>{{ $ticket->due_date->format('Y-m-d') }}</td>
    @isset($actions)
    <td>{{ $actions }}</td>
    @endisset
</tr>