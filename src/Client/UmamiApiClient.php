<?php

namespace Inspyrenees\UmamiBundle\Client;

use Inspyrenees\UmamiBundle\Exception\UmamiApiException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UmamiApiClient implements UmamiClientInterface
{
    private ?string $token = null;
    private HttpClientInterface $httpClient;
    private string $umamiUrl;
    private string $umamiUsername;
    private string $umamiPassword;
    private string $websiteId;
    private int $defaultDaysBack;
    private TimeRangeResolver $timeRangeResolver;

    public function __construct(
        HttpClientInterface $httpClient,
        string              $umamiUrl,
        string              $umamiUsername,
        string              $umamiPassword,
        string              $websiteId,
        int                 $defaultDaysBack = 30,
        ?TimeRangeResolver  $timeRangeResolver = null
    )
    {
        $this->httpClient = $httpClient;
        $this->umamiUrl = rtrim($umamiUrl, '/');
        $this->umamiUsername = $umamiUsername;
        $this->umamiPassword = $umamiPassword;
        $this->websiteId = $websiteId;
        $this->defaultDaysBack = $defaultDaysBack;
        $this->timeRangeResolver = $timeRangeResolver ?? new TimeRangeResolver();
    }

    /**
     * Authenticate with Umami API and retrieve token
     *
     * @throws TransportExceptionInterface
     */
    private function authenticate(): string
    {
        if (null !== $this->token) {
            return $this->token;
        }

        $response = $this->httpClient->request('POST', $this->umamiUrl . '/api/auth/login', [
            'json' => [
                'username' => $this->umamiUsername,
                'password' => $this->umamiPassword,
            ],
        ]);

        $data = $response->toArray();
        $this->token = $data['token'];

        return $this->token;
    }

    /**
     * Clear the authentication token (forces re-authentication on next request)
     */
    public function clearToken(): void
    {
        $this->token = null;
    }

    /**
     * Get page metrics for a specific time period
     *
     * @param int|null $daysBack Number of days to look back (default: configured value or 30)
     * @return array<string, mixed>
     */
    public function getPageMetrics(?int $daysBack = null, string $type = 'path'): array
    {
        $daysBack ??= $this->defaultDaysBack;
        [$startAt, $endAt] = $this->timeRangeResolver->resolve($daysBack);

        return $this->get(
            sprintf('/api/websites/%s/metrics', $this->websiteId),
            [
                'startAt' => $startAt,
                'endAt' => $endAt,
                'type' => $type,
            ]
        );
    }

    /**
     * Get website statistics
     *
     * @param int|null $daysBack Number of days to look back
     * @return array<string, mixed>
     * @throws TransportExceptionInterface
     */
    public function getStats(?int $daysBack = null): array
    {
        $daysBack ??= $this->defaultDaysBack;
        [$startAt, $endAt] = $this->timeRangeResolver->resolve($daysBack);

        return $this->get(
            sprintf('/api/websites/%s/stats', $this->websiteId),
            [
                'startAt' => $startAt,
                'endAt' => $endAt,
            ]
        );
    }

    /**
     * Get metrics by type (url, referrer, browser, os, device, country, event)
     *
     * @param string $type The metric type
     * @param int|null $daysBack Number of days to look back
     * @return array<string, mixed>
     * @throws TransportExceptionInterface
     */
    public function getMetricsByType(string $type, ?int $daysBack = null): array
    {
        $daysBack ??= $this->defaultDaysBack;
        [$startAt, $endAt] = $this->timeRangeResolver->resolve($daysBack);

        return $this->get(
            sprintf('/api/websites/%s/metrics', $this->websiteId),
            [
                'startAt' => $startAt,
                'endAt' => $endAt,
                'type' => $type,
            ]
        );
    }

    /**
     * Get referrer metrics
     */
    public function getReferrers(?int $daysBack = null): array
    {
        return $this->getMetricsByType('referrer', $daysBack);
    }

    /**
     * Get browser metrics
     */
    public function getBrowsers(?int $daysBack = null): array
    {
        return $this->getMetricsByType('browser', $daysBack);
    }

    /**
     * Get operating system metrics
     */
    public function getOperatingSystems(?int $daysBack = null): array
    {
        return $this->getMetricsByType('os', $daysBack);
    }

    /**
     * Get device metrics
     */
    public function getDevices(?int $daysBack = null): array
    {
        return $this->getMetricsByType('device', $daysBack);
    }

    /**
     * Get country metrics
     */
    public function getCountries(?int $daysBack = null): array
    {
        return $this->getMetricsByType('country', $daysBack);
    }

    /**
     * Get event metrics
     */
    public function getEvents(?int $daysBack = null): array
    {
        return $this->getMetricsByType('event', $daysBack);
    }

    /**
     * Get page views over time
     *
     * @param string $unit Time unit (year, month, hour, day)
     * @param string $timezone Timezone (e.g., 'Europe/Paris')
     * @param int|null $daysBack Number of days to look back
     * @return array<string, mixed>
     * @throws TransportExceptionInterface
     */
    public function getPageViews(string $unit = 'day', string $timezone = 'UTC', ?int $daysBack = null): array
    {
        $daysBack ??= $this->defaultDaysBack;
        $token = $this->authenticate();

        [$startAt, $endAt] = $this->timeRangeResolver->resolve($daysBack);

        return $this->get(
            sprintf('/api/websites/%s/pageviews', $this->websiteId),
            [
                'startAt' => $startAt,
                'endAt' => $endAt,
                'unit' => $unit,
                'timezone' => $timezone,
            ]
        );
    }

    /**
     * Get active users (real-time)
     *
     * @return array<string, mixed>
     * @throws TransportExceptionInterface
     */
    public function getActiveUsers(): array
    {
        return $this->get(
            sprintf('/api/websites/%s/active', $this->websiteId),
        );
    }

    private function get(string $endpoint, array $query = []): array
    {
        try {
            return $this->httpClient->request(
                'GET',
                $this->umamiUrl . $endpoint,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->authenticate(),
                    ],
                    'query' => $query,
                ]
            )->toArray();
        } catch (HttpExceptionInterface $e) {
            throw new UmamiApiException(
                sprintf('Umami API error (%d): %s', $e->getResponse()->getStatusCode(), $e->getMessage()),
                $e->getResponse()->getStatusCode(),
                $e
            );
        }
    }

}
