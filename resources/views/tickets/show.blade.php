<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>{{ $ticket->title }}</h1>

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

<ul>
    <li>Project: {{ $ticket->project->name }}</li>
    <li>Category: {{ $ticket->category->label() }}</li>
    <li>Status: {{ $ticket->status->label() }}</li>
    <li>Priority: {{ $ticket->priority->label() }}</li>
    <li>Created by: {{ $ticket->creator->name }}</li>
    <li>Assignee: {{ $ticket->assignee?->name ?? 'Unassigned' }}</li>
    <li>Due: {{ $ticket->due_date->format('Y-m-d') }}</li>
</ul>

<p>{{ $ticket->description }}</p>

<h2>Comments</h2>

@forelse ($ticket->comments as $comment)
    <div>
        <strong>{{ $comment->user->name }}</strong>
        <small>{{ $comment->created_at->diffForHumans() }}</small>
        <p>{{ $comment->body }}</p>

        @can('delete', $comment)
            <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                  onsubmit="return confirm('Delete this comment?')">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        @endcan
    </div>
    <hr>
@empty
    <p>No comments yet.</p>
@endforelse

@can('create', [\App\Models\Comment::class, $ticket])
    <form method="POST" action="{{ route('comments.store', $ticket) }}">
        @csrf
        <label for="body">Add a comment</label>
        <textarea id="body" name="body" required>{{ old('body') }}</textarea>
        <button type="submit">Post comment</button>
    </form>
@endcan

<a href="{{ route('projects.show', $ticket->project) }}">Back to project</a>
</body>
</html>