<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Project;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(StoreTicketRequest $request, Project $project, TicketService $ticketService)
    {
        $ticketService->create($request->validated(), $request->user(), $project);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Ticket created successfully.');
    }
    public function create(Project $project, TicketService $ticketService)
    {
        return view('tickets.create', ['project' => $project, 'assignees' => $ticketService->assignableUsers($project)]);
    }
    public function edit(Ticket $ticket, TicketService $ticketService)
    {
        return view('tickets.edit', [
            'ticket' => $ticket,
            'assignees' => $ticketService->assignableUsers($ticket->project),
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket, TicketService $ticketService)
    {
        $ticketService->update($ticket, $request->validated());

        return redirect()->route('projects.show', $ticket->project)
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket, TicketService $ticketService)
    {
        $project = $ticket->project;

        $ticketService->delete($ticket);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Ticket deleted successfully.');
    }
    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket, TicketService $ticketService)
{
    $ticketService->updateStatus($ticket, $request->enum('status', TicketStatus::class));

    return back()->with('success', 'Status updated.');
}
public function mine(Request $request, TicketService $ticketService)
{
    return view('tickets.mine', [
        'tickets' => $ticketService->myTickets($request->user()),
    ]);
}
public function show(Ticket $ticket, TicketService $ticketService)
{
    return view('tickets.show', [
        'ticket' => $ticketService->loadDetails($ticket),
    ]);
}
}
