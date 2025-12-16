<?php

namespace Inspyrenees\UmamiBundle\Client;

interface UmamiClientInterface
{
    public function getPageMetrics(?int $daysBack = null): array;

    public function getStats(?int $daysBack = null): array;

    public function getMetricsByType(string $type, ?int $daysBack = null): array;

    public function getReferrers(?int $daysBack = null): array;

    public function getBrowsers(?int $daysBack = null): array;

    public function getOperatingSystems(?int $daysBack = null): array;

    public function getDevices(?int $daysBack = null): array;

    public function getCountries(?int $daysBack = null): array;

    public function getEvents(?int $daysBack = null): array;

    public function getPageViews(
        string $unit = 'day',
        string $timezone = 'UTC',
        ?int $daysBack = null
    ): array;

    public function getActiveUsers(): array;

    public function clearToken(): void;
}
