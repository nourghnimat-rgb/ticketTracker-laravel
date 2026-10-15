<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_a_project_that_has_tickets(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::create(['name' => 'Demo', 'created_by' => $admin->id]);
        $ticket = Ticket::create([
            'title' => 'T', 'description' => 'D', 'category' => 'bug',
            'status' => 'open', 'priority' => 'low',
            'due_date' => now()->addDay(),
            'project_id' => $project->id, 'created_by' => $admin->id,
        ]);
        $ticket->comments()->create(['body' => 'hi', 'user_id' => $admin->id]);

        $this->actingAs($admin)
            ->delete(route('projects.destroy', $project))
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }
}
