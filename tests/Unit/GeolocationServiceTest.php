<?php

namespace Tests\Unit;

use App\Services\GeolocationService;
use Tests\TestCase;

class GeolocationServiceTest extends TestCase
{
  public function test_location_within_office_radius(): void
    {
        $service = new GeolocationService();

        $this->assertTrue($service->isWithinOfficeRadius(-6.242792163317656, 106.84609367942863));
    }

    public function test_location_outside_office_radius(): void
    {
        $service = new GeolocationService();

        $this->assertFalse($service->isWithinOfficeRadius(-6.250000, 106.900000));
    }
}
