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