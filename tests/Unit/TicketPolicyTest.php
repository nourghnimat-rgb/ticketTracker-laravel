<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\TicketPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    private TicketPolicy $policy;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new TicketPolicy;

        $admin = User::factory()->create(['role' => 'admin']);
        $this->project = Project::create(['name' => 'Demo', 'created_by' => $admin->id]);
    }

    private function member(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->project->users()->attach($user);

        return $user;
    }

    private function ticket(?User $assignee = null): Ticket
    {
        $creator = User::factory()->create(['role' => 'manager']);

        return Ticket::create([
            'title' => 'Ticket',
            'description' => 'Description',
            'category' => 'bug',
            'status' => 'open',
            'priority' => 'medium',
            'due_date' => now()->addDays(3)->toDateString(),
            'project_id' => $this->project->id,
            'created_by' => $creator->id,
            'assignee_id' => $assignee?->id,
        ]);
    }

    public function test_admin_can_do_everything_even_without_membership(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = $this->ticket();

        $this->assertTrue($this->policy->view($admin, $ticket));
        $this->assertTrue($this->policy->create($admin, $this->project));
        $this->assertTrue($this->policy->update($admin, $ticket));
        $this->assertTrue($this->policy->delete($admin, $ticket));
    }

    public function test_manager_in_project_can_manage_tickets(): void
    {
        $manager = $this->member('manager');
        $ticket = $this->ticket();

        $this->assertTrue($this->policy->create($manager, $this->project));
        $this->assertTrue($this->policy->update($manager, $ticket));
        $this->assertTrue($this->policy->delete($manager, $ticket));
    }

    public function test_manager_outside_project_is_denied(): void
    {
        $outsider = User::factory()->create(['role' => 'manager']);
        $ticket = $this->ticket();

        $this->assertFalse($this->policy->view($outsider, $ticket));
        $this->assertFalse($this->policy->create($outsider, $this->project));
        $this->assertFalse($this->policy->update($outsider, $ticket));
        $this->assertFalse($this->policy->delete($outsider, $ticket));
    }

    public function test_user_can_view_but_not_create_update_or_delete(): void
    {
        $user = $this->member('user');
        $ticket = $this->ticket();

        $this->assertTrue($this->policy->view($user, $ticket));
        $this->assertFalse($this->policy->create($user, $this->project));
        $this->assertFalse($this->policy->update($user, $ticket));
        $this->assertFalse($this->policy->delete($user, $ticket));
    }

    public function test_only_the_assigned_user_can_update_status(): void
    {
        $assigned = $this->member('user');
        $other = $this->member('user');
        $ticket = $this->ticket($assigned);

        $this->assertTrue($this->policy->updateStatus($assigned, $ticket));
        $this->assertFalse($this->policy->updateStatus($other, $ticket));
    }

    public function test_manager_in_project_can_update_status(): void
    {
        $manager = $this->member('manager');

        $this->assertTrue($this->policy->updateStatus($manager, $this->ticket()));
    }
}