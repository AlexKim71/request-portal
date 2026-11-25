<?php

namespace Tests\Feature;

use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    public function test_the_health_endpoint_reports_application_availability(): void
    {
        $this->get('/up')
            ->assertOk()
            ->assertSeeText('Application up');
    }
}
