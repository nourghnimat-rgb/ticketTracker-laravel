<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Services\CommentService;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket, CommentService $commentService)
    {
        $commentService->create($ticket, $request->user(), $request->validated());

        return back()->with('success', 'Comment added.');
    }

    public function destroy(Comment $comment, CommentService $commentService)
    {
        $commentService->delete($comment);

        return back()->with('success', 'Comment deleted.');
    }
}
