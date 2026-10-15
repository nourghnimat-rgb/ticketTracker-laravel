<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Jobs\SendTicketNotification;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TicketService
{
    public function create(array $data, User $creator, Project $project): Ticket
    {
        $ticket = Ticket::create([
            ...$data,
            'project_id' => $project->id,
            'created_by' => $creator->id,
        ]);

        if ($ticket->assignee_id) {
            SendTicketNotification::dispatch(
                $ticket,
                [$ticket->assignee_id],
                "You were assigned to ticket: {$ticket->title}"
            );
        }

        return $ticket;
    }

    public function assignableUsers(Project $project): Collection
    {
        return $project->users()->where('role', UserRole::User->value)
            ->orderBy('name')->get();
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        $ticket->update($data);

        if ($ticket->wasChanged('assignee_id') && $ticket->assignee_id) {
            SendTicketNotification::dispatch(
                $ticket,
                [$ticket->assignee_id],
                "You were assigned to ticket: {$ticket->title}"
            );
        }

        return $ticket;
    }

    public function delete(Ticket $ticket): void
    {
        $ticket->delete();
    }

    public function updateStatus(Ticket $ticket, TicketStatus $status): Ticket
    {
        $ticket->update(['status' => $status]);

        return $ticket;
    }

    public function myTickets(User $user): Collection
    {
        $query = $user->role === UserRole::User
            ? Ticket::where('assignee_id', $user->id)
            : Ticket::where('created_by', $user->id);

        return $query->with(['project', 'assignee'])->latest()->get();
    }

    public function notifyOverdue(): int
    {
        $overdue = Ticket::with('project')
            ->whereDate('due_date', '<', today())
            ->whereNotIn('status', [TicketStatus::Resolved->value, TicketStatus::Closed->value])
            ->get();

        $adminIds = User::where('role', UserRole::Admin->value)
            ->pluck('id');

        foreach ($overdue as $ticket) {
            $managerIds = $ticket->project->users()
                ->where('role', UserRole::Manager->value)
                ->pluck('users.id');

            $recipientIds = $managerIds
                ->merge($adminIds)
                ->push($ticket->assignee_id)
                ->filter()
                ->unique()
                ->values()
                ->all();

            SendTicketNotification::dispatch(
                $ticket,
                $recipientIds,
                "Ticket is overdue: {$ticket->title}"
            );
        }

        return $overdue->count();
    }

    public function loadDetails(Ticket $ticket): Ticket
    {
        return $ticket->load(['project', 'creator', 'assignee', 'comments.user']);
    }
}
