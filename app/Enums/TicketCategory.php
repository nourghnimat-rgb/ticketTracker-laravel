<?php

namespace App\Enums;

enum TicketCategory: string
{
    case Bug = 'bug';
    case Feature = 'feature';
    case Support = 'support';
    case Improvement = 'improvement';
    case Other = 'other';

    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }
}