<?php

namespace App\Http\Requests;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Comment;

class UpdateTicketRequest extends FormRequest
{
    
    public function authorize(): bool
{
    return $this->user()?->can('update', $this->route('ticket')) ?? false;
}

public function rules(): array
{
    $ticket = $this->route('ticket');

    $dueDate = ['required', 'date'];
    if ($this->input('due_date') !== $ticket->due_date->toDateString()) {
        $dueDate[] = 'after_or_equal:today';
    }

    return [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'due_date' => $dueDate,
        'category' => ['required', Rule::enum(TicketCategory::class)],
        'priority' => ['required', Rule::enum(TicketPriority::class)],
        'status' => ['required', Rule::enum(TicketStatus::class)],
        'assignee_id' => [
            'nullable',
            Rule::exists('users', 'id')->where('role', 'user'),
            Rule::exists('project_user', 'user_id')->where('project_id', $ticket->project_id),
        ],
    ];
}
}
