<?php

namespace App\Services;

class GeolocationService
{
    public function haversineMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1);
        $latTo = deg2rad($lat2);
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) ** 2
            + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function isWithinOfficeRadius(float $lat, float $lng): bool
    {
        $distance = $this->haversineMeters(
            config('attendance.office.latitude'),
            config('attendance.office.longitude'),
            $lat,
            $lng
        );

        return $distance <= config('attendance.office.radius_m');
    }

    public function distanceFromOfficeMeters(float $lat, float $lng): float
    {
        return $this->haversineMeters(
            config('attendance.office.latitude'),
            config('attendance.office.longitude'),
            $lat,
            $lng
        );
    }
}
