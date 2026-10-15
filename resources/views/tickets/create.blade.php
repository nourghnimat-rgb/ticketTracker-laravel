<x-layout title="New ticket">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">New ticket in {{ $project->name }}</h1>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary btn-sm">Back to project</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('tickets.store', $project) }}">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input id="title" name="title" value="{{ old('title') }}"
                           class="form-control @error('title') is-invalid @enderror" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category" class="form-select @error('category') is-invalid @enderror" required>
                            @foreach (\App\Enums\TicketCategory::cases() as $category)
                                <option value="{{ $category->value }}" @selected(old('category') === $category->value)>
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
                                <option value="{{ $priority->value }}" @selected(old('priority') === $priority->value)>
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
                                <option value="{{ $status->value }}" @selected(old('status') === $status->value)>
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
                        <input id="due_date" type="date" name="due_date" min="{{ now()->toDateString() }}"
                               value="{{ old('due_date') }}"
                               class="form-control @error('due_date') is-invalid @enderror" required>
                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="assignee_id" class="form-label">Assignee</label>
                        <select id="assignee_id" name="assignee_id" class="form-select @error('assignee_id') is-invalid @enderror">
                            <option value="">Unassigned</option>
                            @foreach ($assignees as $assignee)
                                <option value="{{ $assignee->id }}" @selected((string) old('assignee_id') === (string) $assignee->id)>
                                    {{ $assignee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assignee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create ticket</button>
            </form>
        </div>
    </div>
</x-layout>