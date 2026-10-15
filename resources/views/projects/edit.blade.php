<x-layout title="Edit project">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Edit project</h1>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">Back to projects</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input id="name" name="name" value="{{ old('name', $project->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
        </div>
    </div>
</x-layout>