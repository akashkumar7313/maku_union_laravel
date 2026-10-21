<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Officer = 'officer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'प्रशासक',
            self::Officer => 'अधिकारी',
        };
    }
}
