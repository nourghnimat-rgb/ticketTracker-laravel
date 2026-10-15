<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendTicketNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public array $recipientIds,
        public string $message,
    ) {}

    public function handle(): void
    {
        $users = User::whereIn('id', $this->recipientIds)->get();

        Notification::send($users, new TicketNotification($this->ticket, $this->message));
    }
}
