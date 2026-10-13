<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>New ticket in {{ $project->name }}</h1>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('tickets.store', $project) }}">
    @csrf

    <label for="title">Title</label>
    <input id="title" name="title" value="{{ old('title') }}" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" required>{{ old('description') }}</textarea>

    <label for="category">Category</label>
    <select id="category" name="category" required>
        @foreach (\App\Enums\TicketCategory::cases() as $category)
            <option value="{{ $category->value }}" @selected(old('category') === $category->value)>
                {{ $category->label() }}
            </option>
        @endforeach
    </select>

    <label for="priority">Priority</label>
    <select id="priority" name="priority" required>
        @foreach (\App\Enums\TicketPriority::cases() as $priority)
            <option value="{{ $priority->value }}" @selected(old('priority') === $priority->value)>
                {{ $priority->label() }}
            </option>
        @endforeach
    </select>

    <label for="status">Status</label>
    <select id="status" name="status" required>
        @foreach (\App\Enums\TicketStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected(old('status') === $status->value)>
                {{ $status->label() }}
            </option>
        @endforeach
    </select>

    <label for="due_date">Due date</label>
    <input id="due_date" type="date" name="due_date" min="{{ now()->toDateString() }}" value="{{ old('due_date') }}" required>

    <label for="assignee_id">Assignee</label>
    <select id="assignee_id" name="assignee_id">
        <option value="">Unassigned</option>
        @foreach ($assignees as $assignee)
            <option value="{{ $assignee->id }}" @selected((string) old('assignee_id') === (string) $assignee->id)>
                {{ $assignee->name }}
            </option>
        @endforeach
    </select>

    <button type="submit">Create ticket</button>
</form>

<a href="{{ route('projects.show', $project) }}">Back to project</a>
</body>
</html>