<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
   use Illuminate\Database\Eloquent\Collection;


class TicketService
{
    public function create(array $data, User $creator, Project $project): Ticket
{
    return Ticket::create([
        ...$data,
        'project_id' => $project->id,
        'created_by' => $creator->id,
    ]);
}
public function assignableUsers (Project $project):Collection
{
    return $project->users()->where('role','user')->orderBy('name')->get();

}
public function update(Ticket $ticket, array $data): Ticket
{
    $ticket->update($data);

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
    $query = $user->role === 'user'
        ? Ticket::where('assignee_id', $user->id)
        : Ticket::where('created_by', $user->id);

    return $query->with(['project', 'assignee'])->latest()->get();
}
}