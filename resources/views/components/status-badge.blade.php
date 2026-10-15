@props(['status'])

@php
    $color = match ($status) {
        \App\Enums\TicketStatus::Open => 'secondary',
        \App\Enums\TicketStatus::InProgress => 'primary',
        \App\Enums\TicketStatus::Resolved => 'success',
        \App\Enums\TicketStatus::Closed => 'dark',
    };
@endphp

<span class="badge text-bg-{{ $color }}">{{ $status->label() }}</span>