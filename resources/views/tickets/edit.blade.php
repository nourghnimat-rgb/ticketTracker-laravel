<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Edit ticket in {{ $ticket->project->name }}</h1>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('tickets.update', $ticket) }}">
    @csrf
    @method('PUT')

    <label for="title">Title</label>
    <input id="title" name="title" value="{{ old('title', $ticket->title) }}" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" required>{{ old('description', $ticket->description) }}</textarea>

    <label for="category">Category</label>
    <select id="category" name="category" required>
        @foreach (\App\Enums\TicketCategory::cases() as $category)
            <option value="{{ $category->value }}" @selected(old('category', $ticket->category->value) === $category->value)>
                {{ $category->label() }}
            </option>
        @endforeach
    </select>

    <label for="priority">Priority</label>
    <select id="priority" name="priority" required>
        @foreach (\App\Enums\TicketPriority::cases() as $priority)
            <option value="{{ $priority->value }}" @selected(old('priority', $ticket->priority->value) === $priority->value)>
                {{ $priority->label() }}
            </option>
        @endforeach
    </select>

    <label for="status">Status</label>
    <select id="status" name="status" required>
        @foreach (\App\Enums\TicketStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected(old('status', $ticket->status->value) === $status->value)>
                {{ $status->label() }}
            </option>
        @endforeach
    </select>

    <label for="due_date">Due date</label>
    <input id="due_date" type="date" name="due_date" value="{{ old('due_date', $ticket->due_date->toDateString()) }}" required>

    <label for="assignee_id">Assignee</label>
    <select id="assignee_id" name="assignee_id">
        <option value="">Unassigned</option>
        @foreach ($assignees as $assignee)
            <option value="{{ $assignee->id }}" @selected((string) old('assignee_id', $ticket->assignee_id) === (string) $assignee->id)>
                {{ $assignee->name }}
            </option>
        @endforeach
    </select>

    <button type="submit">Save changes</button>
</form>

<a href="{{ route('projects.show', $ticket->project) }}">Back to project</a>
</body>
</html>