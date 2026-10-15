<?php

namespace Tests\Feature;

use App\Jobs\SendTicketNotification;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationJobsTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->project = Project::create(['name' => 'Demo', 'created_by' => $this->manager->id]);
        $this->project->users()->attach([$this->manager->id, $this->user->id]);
    }

    private function makeTicket(array $overrides = []): Ticket
    {
        return Ticket::create(array_merge([
            'title' => 'T', 'description' => 'D', 'category' => 'bug',
            'status' => 'open', 'priority' => 'low',
            'due_date' => now()->addDay(),
            'project_id' => $this->project->id,
            'created_by' => $this->manager->id,
            'assignee_id' => $this->user->id,
        ], $overrides));
    }

    public function test_assigning_a_ticket_dispatches_a_notification_job(): void
    {
        Queue::fake();

        $this->actingAs($this->manager)->post(route('tickets.store', $this->project), [
            'title' => 'T', 'description' => 'D',
            'due_date' => now()->addDay()->toDateString(),
            'category' => 'bug', 'priority' => 'low', 'status' => 'open',
            'assignee_id' => $this->user->id,
        ]);

        Queue::assertPushed(SendTicketNotification::class,
            fn ($job) => $job->recipientIds === [$this->user->id]);
    }

    public function test_a_comment_notifies_the_creator_but_not_the_author(): void
    {
        Queue::fake();
        $ticket = $this->makeTicket();

        $this->actingAs($this->user)
            ->post(route('comments.store', $ticket), ['body' => 'hello']);

        Queue::assertPushed(SendTicketNotification::class,
            fn ($job) => $job->recipientIds === [$this->manager->id]);
    }

    public function test_overdue_command_notifies_assignee_managers_and_admins(): void
    {
        Queue::fake();
        $admin = User::factory()->create(['role' => 'admin']);

        $overdue = $this->makeTicket(['due_date' => now()->subDay()]);
        $this->makeTicket(['due_date' => now()->addDay()]);                         // مش متأخرة
        $this->makeTicket(['due_date' => now()->subDay(), 'status' => 'resolved']); // متأخرة بس محلولة

        $this->artisan('tickets:notify-overdue')
            ->expectsOutput('Dispatched notifications for 1 overdue ticket(s).')
            ->assertSuccessful();

        Queue::assertPushed(SendTicketNotification::class, 1);
        Queue::assertPushed(SendTicketNotification::class, fn ($job) =>
            $job->ticket->is($overdue)
            && in_array($this->user->id, $job->recipientIds)
            && in_array($this->manager->id, $job->recipientIds)
            && in_array($admin->id, $job->recipientIds));
    }
}