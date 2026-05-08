<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo       = 'todo';
    case InProgress = 'in_progress';
    case InReview   = 'in_review';
    case Done       = 'done';

    public function label(): string
    {
        return match($this) {
            self::Todo       => 'To Do',
            self::InProgress => 'In Progress',
            self::InReview   => 'In Review',
            self::Done       => 'Done',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Todo       => '#6b7280',
            self::InProgress => '#3b82f6',
            self::InReview   => '#8b5cf6',
            self::Done       => '#10b981',
        };
    }
}
