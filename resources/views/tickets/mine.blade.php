<x-layout title="My Tickets">
    <h1 class="h3 mb-3">My Tickets</h1>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assignee</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                    {{ $ticket->title }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('projects.show', $ticket->project) }}" class="text-decoration-none">
                                    {{ $ticket->project->name }}
                                </a>
                            </td>
                            <td>{{ $ticket->category->label() }}</td>
                            <td><x-status-badge :status="$ticket->status" /></td>
                            <td>{{ $ticket->priority->label() }}</td>
                            <td>{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>
                            <td>{{ $ticket->due_date->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">No tickets.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>