<?php

namespace Inspyrenees\UmamiBundle\Tests\Unit\Client;

use Inspyrenees\UmamiBundle\Client\TimeRangeResolver;
use PHPUnit\Framework\TestCase;

class TimeRangeResolverTest extends TestCase
{
    public function testResolveReturnsValidRange(): void
    {
        $resolver = new TimeRangeResolver();
        [$start, $end] = $resolver->resolve(7);

        $this->assertIsInt($start);
        $this->assertIsInt($end);

        $this->assertLessThan($end, $start);

        $expectedDuration = 7 * 86400 * 1000;
        $actualDuration = $end - $start;

        $this->assertGreaterThanOrEqual($expectedDuration - 1000, $actualDuration);
        $this->assertLessThanOrEqual($expectedDuration + 1000, $actualDuration);
    }
}
