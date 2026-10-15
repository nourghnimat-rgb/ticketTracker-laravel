<?php

namespace Tests\Feature;

use App\Jobs\SendTicketNotification;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CommentPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $manager;

    private User $user;

    private Project $project;

    private Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->user = User::factory()->create(['role' => 'user']);

        $this->project = Project::create(['name' => 'Demo', 'created_by' => $this->admin->id]);
        $this->project->users()->attach([$this->manager->id, $this->user->id]);

        $this->ticket = Ticket::create([
            'title' => 'Test ticket',
            'description' => 'Description',
            'category' => 'bug',
            'status' => 'open',
            'priority' => 'medium',
            'due_date' => now()->addDays(3),
            'project_id' => $this->project->id,
            'created_by' => $this->manager->id,
            'assignee_id' => $this->user->id,
        ]);
    }

    private function comment(User $author): Comment
    {
        return Comment::create([
            'body' => 'A comment',
            'user_id' => $author->id,
            'ticket_id' => $this->ticket->id,
        ]);
    }

    public function test_project_member_can_comment_and_the_creator_is_notified(): void
    {
        Queue::fake();

        $this->actingAs($this->user)
            ->post(route('comments.store', $this->ticket), ['body' => 'Hello'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', ['body' => 'Hello', 'user_id' => $this->user->id]);

        Queue::assertPushed(SendTicketNotification::class, function ($job) {
            return in_array($this->manager->id, $job->recipientIds)
                && ! in_array($this->user->id, $job->recipientIds);
        });
    }

    public function test_user_outside_the_project_cannot_comment(): void
    {
        $outsider = User::factory()->create(['role' => 'user']);

        $this->actingAs($outsider)
            ->post(route('comments.store', $this->ticket), ['body' => 'Hello'])
            ->assertForbidden();
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $comment = $this->comment($this->user);

        $this->actingAs($this->admin)
            ->delete(route('comments.destroy', $comment))
            ->assertRedirect();

        $this->assertModelMissing($comment);
    }

    public function test_manager_can_delete_comments_on_tickets_they_created(): void
    {
        $comment = $this->comment($this->user);

        $this->actingAs($this->manager)
            ->delete(route('comments.destroy', $comment))
            ->assertRedirect();

        $this->assertModelMissing($comment);
    }

    public function test_user_can_only_delete_their_own_comments(): void
    {
        $own = $this->comment($this->user);
        $others = $this->comment($this->manager);

        $this->actingAs($this->user)
            ->delete(route('comments.destroy', $others))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete(route('comments.destroy', $own))
            ->assertRedirect();

        $this->assertModelExists($others);
        $this->assertModelMissing($own);
    }
}
