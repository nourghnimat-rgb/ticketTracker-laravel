<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Create Project</h1>

<form method="POST" action="{{ route('projects.store') }}">
    @csrf

    <label for="name">Project name</label>
    <input id="name" name="name" value="{{ old('name') }}" required>

    @error('name')
        <p>{{ $message }}</p>
    @enderror

    <button type="submit">Create</button>
</form>

<a href="{{ route('projects.index') }}">Back to projects</a>
</body>
</html>