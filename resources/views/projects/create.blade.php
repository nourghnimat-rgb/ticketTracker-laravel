<x-layout title="Create project">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Create project</h1>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">Back to projects</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input id="name" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create project</button>
            </form>
        </div>
    </div>
</x-layout>