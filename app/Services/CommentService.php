<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;

class CommentService
{
    public function create(Ticket $ticket, User $author, array $data): Comment
    {
        return $ticket->comments()->create([
            'body' => $data['body'],
            'user_id' => $author->id,
        ]);
    }

    public function delete(Comment $comment): void
    {
        $comment->delete();
    }
}