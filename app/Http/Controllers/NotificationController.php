<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request, NotificationService $notificationService)
    {
        return view('notifications.index', [
            'notifications' => $notificationService->listFor($request->user()),
        ]);
    }

    public function read(Request $request, string $id, NotificationService $notificationService)
    {
        $notification = $notificationService->markAsRead($request->user(), $id);

        return redirect()->route('tickets.show', $notification->data['ticket_id']);
    }

    public function readAll(Request $request, NotificationService $notificationService)
    {
        $notificationService->markAllAsRead($request->user());

        return back()->with('success', 'All notifications marked as read.');
    }
}
