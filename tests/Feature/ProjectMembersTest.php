<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectMembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_removing_a_member_unassigns_their_tickets(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $removed = User::factory()->create(['role' => 'user']);
        $kept = User::factory()->create(['role' => 'user']);

        $project = Project::create(['name' => 'Demo', 'created_by' => $admin->id]);
        $project->users()->attach([$removed->id, $kept->id]);

        $ticket = Ticket::create([
            'title' => 'Test ticket',
            'description' => 'Description',
            'category' => 'bug',
            'status' => 'open',
            'priority' => 'medium',
            'due_date' => now()->addDays(3),
            'project_id' => $project->id,
            'created_by' => $admin->id,
            'assignee_id' => $removed->id,
        ]);

        $this->actingAs($admin)
            ->put(route('projects.members.update', $project), ['members' => [$kept->id]])
            ->assertRedirect();

        $this->assertNull($ticket->fresh()->assignee_id);
        $this->assertTrue($project->users()->whereKey($kept->id)->exists());
        $this->assertFalse($project->users()->whereKey($removed->id)->exists());
    }
}
