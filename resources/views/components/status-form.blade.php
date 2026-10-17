@props(['ticket'])

<form method="POST" action="{{ route('tickets.status.update', $ticket) }}"
    {{ $attributes->merge(['class' => 'd-flex gap-2']) }}>
    @csrf
    @method('PATCH')
    <select name="status" class="form-select form-select-sm">
        @foreach (\App\Enums\TicketStatus::cases() as $status)
        <option value="{{ $status->value }}" @selected($ticket->status === $status)>
            {{ $status->label() }}
        </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
</form>