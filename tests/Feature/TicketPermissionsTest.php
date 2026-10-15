<?php

namespace Tests\Feature;

use App\Jobs\SendTicketNotification;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TicketPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private function project(): Project
    {
        $admin = User::factory()->create(['role' => 'admin']);

        return Project::create(['name' => 'Demo', 'created_by' => $admin->id]);
    }

    private function ticketData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Test ticket',
            'description' => 'Description',
            'category' => 'bug',
            'status' => 'open',
            'priority' => 'medium',
            'due_date' => now()->addDays(3)->toDateString(),
        ], $overrides);
    }

    private function makeTicket(Project $project, User $creator, ?User $assignee = null): Ticket
    {
        return Ticket::create($this->ticketData() + [
            'project_id' => $project->id,
            'created_by' => $creator->id,
            'assignee_id' => $assignee?->id,
        ]);
    }

    public function test_user_cannot_create_tickets(): void
    {
        $project = $this->project();
        $user = User::factory()->create(['role' => 'user']);
        $project->users()->attach($user);

        $this->actingAs($user)
            ->post(route('tickets.store', $project), $this->ticketData())
            ->assertForbidden();
    }

    public function test_manager_outside_the_project_cannot_create_tickets(): void
    {
        $project = $this->project();
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)
            ->post(route('tickets.store', $project), $this->ticketData())
            ->assertForbidden();
    }

    public function test_manager_in_the_project_can_create_and_assign_a_ticket(): void
    {
        Queue::fake();

        $project = $this->project();
        $manager = User::factory()->create(['role' => 'manager']);
        $user = User::factory()->create(['role' => 'user']);
        $project->users()->attach([$manager->id, $user->id]);

        $this->actingAs($manager)
            ->post(route('tickets.store', $project), $this->ticketData(['assignee_id' => $user->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test ticket',
            'created_by' => $manager->id,
            'assignee_id' => $user->id,
        ]);

        Queue::assertPushed(SendTicketNotification::class);
    }

    public function test_ticket_cannot_be_assigned_to_a_user_outside_the_project(): void
    {
        $project = $this->project();
        $manager = User::factory()->create(['role' => 'manager']);
        $outsider = User::factory()->create(['role' => 'user']);
        $project->users()->attach($manager);

        $this->actingAs($manager)
            ->post(route('tickets.store', $project), $this->ticketData(['assignee_id' => $outsider->id]))
            ->assertSessionHasErrors('assignee_id');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_assigned_user_can_change_the_status_of_their_ticket(): void
    {
        $project = $this->project();
        $manager = User::factory()->create(['role' => 'manager']);
        $user = User::factory()->create(['role' => 'user']);
        $project->users()->attach([$manager->id, $user->id]);
        $ticket = $this->makeTicket($project, $manager, $user);

        $this->actingAs($user)
            ->patch(route('tickets.status.update', $ticket), ['status' => 'resolved'])
            ->assertRedirect();

        $this->assertSame('resolved', $ticket->fresh()->status->value);
    }

    public function test_user_cannot_change_the_status_of_a_ticket_assigned_to_someone_else(): void
    {
        $project = $this->project();
        $manager = User::factory()->create(['role' => 'manager']);
        $assignee = User::factory()->create(['role' => 'user']);
        $other = User::factory()->create(['role' => 'user']);
        $project->users()->attach([$manager->id, $assignee->id, $other->id]);
        $ticket = $this->makeTicket($project, $manager, $assignee);

        $this->actingAs($other)
            ->patch(route('tickets.status.update', $ticket), ['status' => 'resolved'])
            ->assertForbidden();

        $this->assertSame('open', $ticket->fresh()->status->value);
    }
}