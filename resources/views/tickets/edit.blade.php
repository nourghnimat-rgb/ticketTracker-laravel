<x-layout title="Edit ticket">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Edit ticket in {{ $ticket->project->name }}</h1>
        <a href="{{ route('projects.show', $ticket->project) }}" class="btn btn-outline-secondary btn-sm">Back to project</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input id="title" name="title" value="{{ old('title', $ticket->title) }}"
                           class="form-control @error('title') is-invalid @enderror" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $ticket->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category" class="form-select @error('category') is-invalid @enderror" required>
                            @foreach (\App\Enums\TicketCategory::cases() as $category)
                                <option value="{{ $category->value }}"
                                    @selected(old('category', $ticket->category->value) === $category->value)>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            @foreach (\App\Enums\TicketPriority::cases() as $priority)
                                <option value="{{ $priority->value }}"
                                    @selected(old('priority', $ticket->priority->value) === $priority->value)>
                                    {{ $priority->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach (\App\Enums\TicketStatus::cases() as $status)
                                <option value="{{ $status->value }}"
                                    @selected(old('status', $ticket->status->value) === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="due_date" class="form-label">Due date</label>
                        <input id="due_date" type="date" name="due_date"
                               value="{{ old('due_date', $ticket->due_date->toDateString()) }}"
                               class="form-control @error('due_date') is-invalid @enderror" required>
                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="assignee_id" class="form-label">Assignee</label>
                        <select id="assignee_id" name="assignee_id" class="form-select @error('assignee_id') is-invalid @enderror">
                            <option value="">Unassigned</option>
                            @foreach ($assignees as $assignee)
                                <option value="{{ $assignee->id }}"
                                    @selected((string) old('assignee_id', $ticket->assignee_id) === (string) $assignee->id)>
                                    {{ $assignee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assignee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
        </div>
    </div>
</x-layout>