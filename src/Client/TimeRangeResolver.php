<?php

namespace Inspyrenees\UmamiBundle\Client;

final class TimeRangeResolver
{
    private const MILLISECONDS_MULTIPLIER = 1000;
    private const SECONDS_PER_DAY = 86400;

    public function resolve(int $daysBack): array
    {
        $endAt = time() * self::MILLISECONDS_MULTIPLIER;
        $startAt = (time() - ($daysBack * self::SECONDS_PER_DAY)) * self::MILLISECONDS_MULTIPLIER;

        return [$startAt, $endAt];
    }
}
