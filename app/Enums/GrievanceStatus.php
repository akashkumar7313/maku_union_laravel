<?php

namespace App\Enums;

enum GrievanceStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Rejected = 'rejected';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'लंबित',
            self::InProgress => 'प्रक्रियाधीन',
            self::Resolved => 'समाधान हो गया',
            self::Rejected => 'अस्वीकृत',
            self::Closed => 'बंद',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::InProgress => 'info',
            self::Resolved => 'success',
            self::Rejected => 'danger',
            self::Closed => 'secondary',
        };
    }
}
