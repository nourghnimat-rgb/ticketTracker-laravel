<?php

namespace App\Console\Commands;

use App\Services\TicketService;
use Illuminate\Console\Command;

class NotifyOverdueTickets extends Command
{
    protected $signature = 'tickets:notify-overdue';

    protected $description = 'Notify assignees, managers and admins about overdue tickets';

    public function handle(TicketService $ticketService): int
    {
        $count = $ticketService->notifyOverdue();

        $this->info("Dispatched notifications for {$count} overdue ticket(s).");

        return self::SUCCESS;
    }
}