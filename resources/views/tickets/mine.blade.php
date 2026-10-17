<x-layout title="My Tickets">
    <h1 class="h3 mb-3">My Tickets</h1>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assignee</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <x-ticket-row :ticket="$ticket" :show-project="true" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layout>