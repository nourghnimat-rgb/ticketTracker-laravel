<?php

namespace App\Http\Requests;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project
            && ($this->user()?->can('create', [Ticket::class, $project]) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'category' => ['required', Rule::enum(TicketCategory::class)],
            'priority' => ['required', Rule::enum(TicketPriority::class)],
            'status' => ['required', Rule::enum(TicketStatus::class)],
            'assignee_id' => [
                'nullable',
                Rule::exists('users', 'id')->where('role', 'user'),
                Rule::exists('project_user', 'user_id')->where('project_id', $this->route('project')->id),
            ],
        ];
    }
}
