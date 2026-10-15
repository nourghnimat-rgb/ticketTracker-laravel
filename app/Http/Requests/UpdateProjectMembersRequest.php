<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectMembersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('project')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'members' => ['sometimes', 'array'],
            'members.*' => [
                'integer',
                'distinct',
                Rule::exists('users', 'id')->whereIn('role', ['manager', 'user'])
            ],
        ];
    }
}
