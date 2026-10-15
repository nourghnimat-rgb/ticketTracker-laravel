<x-layout title="Notifications">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Notifications</h1>

        @if (auth()->user()->unreadNotifications->count())
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm">Mark all as read</button>
            </form>
        @endif
    </div>

    <div class="list-group mb-3">
        @forelse ($notifications as $notification)
            <div class="list-group-item d-flex justify-content-between align-items-center
                        {{ $notification->read_at ? '' : 'list-group-item-primary' }}">
                <div>
                    <div class="fw-semibold">{{ $notification->data['message'] }}</div>
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                </div>

                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">View ticket</button>
                </form>
            </div>
        @empty
            <div class="list-group-item text-muted">No notifications.</div>
        @endforelse
    </div>

    {{ $notifications->links('pagination::bootstrap-5') }}
</x-layout>