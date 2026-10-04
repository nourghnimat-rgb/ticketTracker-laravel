<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
</head>
<body>
    <h1>Edit Project</h1>

    <form method="POST" action="{{ route('projects.update', $project) }}">
        @csrf
        @method('PUT')

        <label for="name">Project name</label>
        <input id="name" name="name"
               value="{{ old('name', $project->name) }}" required>

        @error('name')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Save changes</button>
    </form>

    <a href="{{ route('projects.index') }}">Back to projects</a>
</body>
</html>