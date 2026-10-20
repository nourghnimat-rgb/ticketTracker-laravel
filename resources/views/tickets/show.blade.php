<x-layout :title="$ticket->title">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $ticket->title }}</h1>
        <a href="{{ route('projects.show', $ticket->project) }}" class="btn btn-outline-secondary btn-sm">
            Back to project
        </a>
    </div>

    {{-- Details --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Project:</strong> {{ $ticket->project->name }}</div>
                <div class="col-md-4"><strong>Category:</strong> {{ $ticket->category->label() }}</div>
                <div class="col-md-4"><strong>Status:</strong> <x-status-badge :status="$ticket->status" /></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Priority:</strong> {{ $ticket->priority->label() }}</div>
                <div class="col-md-4"><strong>Created by:</strong> {{ $ticket->creator->name }}</div>
                <div class="col-md-4"><strong>Assignee:</strong> {{ $ticket->assignee?->name ?? 'Unassigned' }}</div>
            </div>
            <div class="mb-3"><strong>Due:</strong> {{ $ticket->due_date->format('Y-m-d') }}</div>
            @can('updateStatus', $ticket)
            <x-status-form :ticket="$ticket" class="mb-3" style="max-width: 320px;" />
            @endcan

            <hr>
            <p class="mb-0">{{ $ticket->description }}</p>
        </div>
    </div>

    {{-- Comments --}}
    <div class="card">
        <div class="card-header"><strong>Comments ({{ $ticket->comments->count() }})</strong></div>

        <ul class="list-group list-group-flush">
            @forelse ($ticket->comments as $comment)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>{{ $comment->user->name }}</strong>
                        <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>

                    @can('delete', $comment)
                    <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                        onsubmit="return confirm('Delete this comment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                    @endcan
                </div>
                <p class="mb-0 mt-2">{{ $comment->body }}</p>
            </li>
            @empty
            <li class="list-group-item text-muted">No comments yet.</li>
            @endforelse
        </ul>

        @can('create', [\App\Models\Comment::class, $ticket])
        <div class="card-body border-top">
            <form method="POST" action="{{ route('comments.store', $ticket) }}">
                @csrf
                <label for="body" class="form-label">Add a comment</label>
                <textarea id="body" name="body" rows="3" class="form-control mb-2" required>{{ old('body') }}</textarea>
                <button type="submit" class="btn btn-primary btn-sm">Post comment</button>
            </form>
        </div>
        @endcan
    </div>
</x-layout>