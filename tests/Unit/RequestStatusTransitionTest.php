<?php

namespace Tests\Unit;

use App\Enums\RequestStatus;
use App\Services\RequestStatusTransition;
use PHPUnit\Framework\TestCase;

class RequestStatusTransitionTest extends TestCase
{
    public function test_new_to_in_progress_allowed(): void
    {
        $this->assertTrue(RequestStatusTransition::canTransition(RequestStatus::NEW, RequestStatus::IN_PROGRESS));
    }

    public function test_new_to_rejected_allowed(): void
    {
        $this->assertTrue(RequestStatusTransition::canTransition(RequestStatus::NEW, RequestStatus::REJECTED));
    }

    public function test_new_to_completed_forbidden(): void
    {
        $this->assertFalse(RequestStatusTransition::canTransition(RequestStatus::NEW, RequestStatus::COMPLETED));
    }

    public function test_completed_is_terminal(): void
    {
        $this->assertFalse(RequestStatusTransition::canTransition(RequestStatus::COMPLETED, RequestStatus::IN_PROGRESS));
        $this->assertFalse(RequestStatusTransition::canTransition(RequestStatus::COMPLETED, RequestStatus::NEW));
    }

    public function test_in_progress_to_completed_allowed(): void
    {
        $this->assertTrue(RequestStatusTransition::canTransition(RequestStatus::IN_PROGRESS, RequestStatus::COMPLETED));
    }
}
