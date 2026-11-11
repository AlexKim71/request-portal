<?php

namespace App\Services;

use App\Enums\RequestStatus;

class RequestStatusTransition
{
    /** @var array<string, string[]> */
    private const ALLOWED = [
        RequestStatus::NEW->value => [
            RequestStatus::IN_PROGRESS->value,
            RequestStatus::REJECTED->value,
        ],
        RequestStatus::IN_PROGRESS->value => [
            RequestStatus::COMPLETED->value,
            RequestStatus::REJECTED->value,
        ],
        RequestStatus::COMPLETED->value => [],
        RequestStatus::REJECTED->value  => [],
    ];

    public static function canTransition(RequestStatus $from, RequestStatus $to): bool
    {
        return in_array($to->value, self::ALLOWED[$from->value] ?? [], true);
    }

    /** @return RequestStatus[] */
    public static function allowedNext(RequestStatus $from): array
    {
        return array_map(
            fn (string $v) => RequestStatus::from($v),
            self::ALLOWED[$from->value] ?? []
        );
    }
}
