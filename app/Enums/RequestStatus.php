<?php

namespace App\Enums;

enum RequestStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Нова',
            self::IN_PROGRESS => 'В роботі',
            self::COMPLETED => 'Завершена',
            self::REJECTED => 'Відхилена',
        };
    }
}
