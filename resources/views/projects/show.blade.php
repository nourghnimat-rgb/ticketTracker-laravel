<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $project->name }}</title>
</head>
<body>
    <h1>{{ $project->name }}</h1>
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

@can('update', $project)
    <h2>Project members</h2>

    <form method="POST"
          action="{{ route('projects.members.update', $project) }}">
        @csrf
        @method('PUT')

        @foreach ($users as $user)
            <label>
                <input type="checkbox"
                       name="members[]"
                       value="{{ $user->id }}"
                       @checked(in_array(
                           $user->id,
                           old('members', $errors->any() ? [] : $project->users->modelKeys())
                       ))>

                {{ $user->name }} ({{ $user->role }})
            </label>
            <br>
        @endforeach

        <button type="submit">Save members</button>
    </form>
@endcan

    <a href="{{ route('projects.index') }}">Back to projects</a>
</body>
</html>