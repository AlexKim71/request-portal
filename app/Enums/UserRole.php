<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENT = 'client';
    case MANAGER = 'manager';

    public function label(): string
    {
        return match ($this) {
            self::CLIENT => 'Клієнт',
            self::MANAGER => 'Менеджер',
        };
    }
}
