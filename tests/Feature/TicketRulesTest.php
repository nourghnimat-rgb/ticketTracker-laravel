<?php

namespace Tests\Feature;

use App\Jobs\SendTicketNotification;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TicketRulesTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private User $member;
    private User $outsider;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->member = User::factory()->create(['role' => 'user']);
        $this->outsider = User::factory()->create(['role' => 'user']);
        $this->project = Project::create(['name' => 'Demo', 'created_by' => $this->manager->id]);
        $this->project->users()->attach([$this->manager->id, $this->member->id]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'T',
            'description' => 'D',
            'due_date' => now()->addDay()->toDateString(),
            'category' => 'bug',
            'priority' => 'low',
            'status' => 'open',
        ], $overrides);
    }

    private function makeTicket(string $status, string $due): Ticket
    {
        return Ticket::create([
            'title' => 'T', 'description' => 'D', 'category' => 'bug',
            'priority' => 'low', 'status' => $status, 'due_date' => $due,
            'project_id' => $this->project->id,
            'created_by' => $this->manager->id,
            'assignee_id' => $this->member->id,
        ]);
    }

    public function test_manager_cannot_assign_to_a_non_member(): void
    {
        $this->actingAs($this->manager)
            ->post(route('tickets.store', $this->project), $this->payload(['assignee_id' => $this->outsider->id]))
            ->assertSessionHasErrors('assignee_id');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_past_due_date_is_rejected_on_create(): void
    {
        $this->actingAs($this->manager)
            ->post(route('tickets.store', $this->project), $this->payload(['due_date' => now()->subDay()->toDateString()]))
            ->assertSessionHasErrors('due_date');
    }

    public function test_non_admin_cannot_create_users(): void
    {
        $this->actingAs($this->manager)
            ->post(route('users.store'), [
                'name' => 'X', 'email' => 'x@example.com',
                'password' => 'password123', 'password_confirmation' => 'password123',
                'role' => 'admin',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'x@example.com']);
    }

    public function test_overdue_command_skips_resolved_and_closed_tickets(): void
    {
        Queue::fake();
        $yesterday = now()->subDay()->toDateString();

        $this->makeTicket('open', $yesterday);
        $this->makeTicket('resolved', $yesterday);
        $this->makeTicket('closed', $yesterday);

        $this->artisan('tickets:notify-overdue')->assertSuccessful();

        Queue::assertPushed(SendTicketNotification::class, 1);
    }

    public function test_job_stores_a_database_notification(): void
    {
        $ticket = $this->makeTicket('open', now()->addDay()->toDateString());

        (new SendTicketNotification($ticket, [$this->member->id], 'Hello'))->handle();

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $this->member->id]);
    }
}