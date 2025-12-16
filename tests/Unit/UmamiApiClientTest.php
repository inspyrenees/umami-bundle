<?php

namespace Inspyrenees\UmamiBundle\Tests\Unit\Client;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Inspyrenees\UmamiBundle\Client\UmamiApiClient;

class UmamiApiClientTest extends TestCase
{
    private UmamiApiClient $client;
    private MockHttpClient $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->client = new UmamiApiClient(
            $this->httpClient,
            'https://analytics.example.com',
            'testuser',
            'testpass',
            'test-website-id',
            30
        );
    }

    public function testGetPageMetricsCallsCorrectEndpoint(): void
    {
        $responses = [
            new MockResponse(json_encode(['token' => 'test-token'])),
            new MockResponse(json_encode([
                ['x' => '/home', 'y' => 100],
                ['x' => '/about', 'y' => 50],
            ])),
        ];

        $this->httpClient->setResponseFactory($responses);

        $metrics = $this->client->getPageMetrics(7);

        $this->assertIsArray($metrics);
        $this->assertCount(2, $metrics);
        $this->assertEquals('/home', $metrics[0]['x']);
        $this->assertEquals(100, $metrics[0]['y']);
    }

    public function testGetStatsReturnsCorrectData(): void
    {
        $responses = [
            new MockResponse(json_encode(['token' => 'test-token'])),
            new MockResponse(json_encode([
                'pageviews' => ['value' => 5000, 'change' => 10],
                'visitors' => ['value' => 2000, 'change' => 5],
            ])),
        ];

        $this->httpClient->setResponseFactory($responses);

        $stats = $this->client->getStats(30);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('pageviews', $stats);
        $this->assertArrayHasKey('visitors', $stats);
        $this->assertEquals(5000, $stats['pageviews']['value']);
    }

    public function testClearTokenResetsAuthentication(): void
    {
        $this->httpClient->setResponseFactory([
            new MockResponse(json_encode(['token' => 'first-token'])),
            new MockResponse(json_encode(['x' => 'data'])),
            new MockResponse(json_encode(['token' => 'second-token'])),
            new MockResponse(json_encode(['x' => 'data'])),
        ]);

        $this->client->getPageMetrics();

        $this->client->clearToken();

        $this->client->getPageMetrics();

        $this->assertSame(4, $this->httpClient->getRequestsCount());
    }


    public function testGetReferrersUsesCorrectType(): void
    {
        $responses = [
            new MockResponse(json_encode(['token' => 'test-token'])),
            new MockResponse(json_encode([
                ['x' => 'google.com', 'y' => 500],
            ])),
        ];

        $this->httpClient->setResponseFactory($responses);

        $referrers = $this->client->getReferrers(7);

        $this->assertIsArray($referrers);
        $this->assertCount(1, $referrers);
    }

    public function testGetActiveUsersReturnsData(): void
    {
        $responses = [
            new MockResponse(json_encode(['token' => 'test-token'])),
            new MockResponse(json_encode([
                ['x' => '/page1', 'y' => 5],
                ['x' => '/page2', 'y' => 3],
            ])),
        ];

        $this->httpClient->setResponseFactory($responses);

        $activeUsers = $this->client->getActiveUsers();

        $this->assertIsArray($activeUsers);
    }

    public function testThrowsExceptionOnHttpError(): void
    {
        $this->httpClient->setResponseFactory([
            new MockResponse('', ['http_code' => 401]),
        ]);

        $this->expectException(\Inspyrenees\UmamiBundle\Exception\UmamiApiException::class);

        $this->client->getActiveUsers();
    }

}
