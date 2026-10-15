@props(['title' => 'Ticket Tracker'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Ticket Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('projects.index') }}">Ticket Tracker</a>

            @auth
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('projects.index') }}">Projects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tickets.mine') }}">My Tickets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('notifications.index') }}">
                        Notifications
                        @if (auth()->user()->unreadNotifications()->count())
                        <span class="badge text-bg-danger">{{ auth()->user()->unreadNotifications()->count() }}</span>
                        @endif
                    </a>
                </li>
                @can('create', \App\Models\User::class)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.create') }}">Create User</a>
                </li>
                @endcan
            </ul>

            <span class="navbar-text me-3">{{ auth()->user()->name }}</span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
            @endauth
        </div>
    </nav>

    <main class="container">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{ $slot }}
    </main>

</body>

</html>