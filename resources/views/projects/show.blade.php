<x-layout :title="$project->name">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $project->name }}</h1>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">Back to projects</a>
    </div>

    {{-- Tickets --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Tickets</strong>
            @can('create', [\App\Models\Ticket::class, $project])
            <a href="{{ route('tickets.create', $project) }}" class="btn btn-primary btn-sm">New ticket</a>
            @endcan
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assignee</th>
                        <th>Due</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($project->tickets as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                {{ $ticket->title }}
                            </a>
                        </td>
                        <td>{{ $ticket->category->label() }}</td>
                        <td><x-status-badge :status="$ticket->status" /></td>
                        <td>{{ $ticket->priority->label() }}</td>
                        <td>{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>
                        <td>{{ $ticket->due_date->format('Y-m-d') }}</td>
                        <td>
                            @can('update', $ticket)
                            <div class="d-flex gap-2">
                                <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-outline-secondary">Edit</a>

                                @can('delete', $ticket)
                                <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                    onsubmit="return confirm('Delete this ticket?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                                @endcan
                            </div>
                            @elsecan('updateStatus', $ticket)
                            <form method="POST" action="{{ route('tickets.status.update', $ticket) }}"
                                class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm">
                                    @foreach (\App\Enums\TicketStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($ticket->status === $status)>
                                        {{ $status->label() }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-muted">No tickets yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Members --}}
    <div class="card">
        <div class="card-header"><strong>Members</strong></div>
        <div class="card-body">
            <ul class="mb-0">
                @forelse ($project->users as $member)
                <li>{{ $member->name }} ({{ $member->role->label() }})</li>
                @empty
                <li class="text-muted">No members yet.</li>
                @endforelse
            </ul>

            @can('update', $project)
            <hr>
            <h2 class="h6">Manage members</h2>

            <form method="POST" action="{{ route('projects.members.update', $project) }}">
                @csrf
                @method('PUT')

                @foreach ($candidates as $user)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="members[]"
                        id="member-{{ $user->id }}" value="{{ $user->id }}"
                        @checked($project->users->contains($user))>
                    <label class="form-check-label" for="member-{{ $user->id }}">
                        {{ $user->name }} ({{ $user->role->label() }})
                    </label>
                </div>
                @endforeach

                <button type="submit" class="btn btn-primary btn-sm mt-3">Save members</button>
            </form>
            @endcan
        </div>
    </div>
</x-layout>