<?php

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'category',
        'status',
        'priority',
        'description',
        'project_id',
        'created_by',
        'assignee_id',
        'due_date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'category' => TicketCategory::class,
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
        ];
    }
    
}
