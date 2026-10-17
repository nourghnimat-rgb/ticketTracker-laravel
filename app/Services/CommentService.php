<?php

namespace App\Services;

use App\Jobs\SendTicketNotification;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;

class CommentService
{
    public function create(Ticket $ticket, User $author, array $data): Comment
    {
        $comment = $ticket->comments()->create([
            'body' => $data['body'],
            'user_id' => $author->id,
        ]);

        $recipientIds = collect([$ticket->created_by, $ticket->assignee_id])
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($recipientIds) {
            SendTicketNotification::dispatch(
                $ticket,
                $recipientIds,
                "{$author->name} commented on ticket: {$ticket->title}"
            );
        }

        return $comment;
    }

    public function delete(Comment $comment): void
    {
        $comment->delete();
    }
}
