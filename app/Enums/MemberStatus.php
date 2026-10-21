<?php

namespace App\Enums;

enum MemberStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'लंबित',
            self::Active => 'सक्रिय',
            self::Expired => 'अवधि समाप्त',
            self::Suspended => 'निलंबित',
            self::Rejected => 'अस्वीकृत',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Active => 'success',
            self::Expired => 'secondary',
            self::Suspended => 'danger',
            self::Rejected => 'danger',
        };
    }
}
