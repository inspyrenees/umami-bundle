# Umami Bundle for Symfony

[![Latest Stable Version](https://img.shields.io/packagist/v/inspyrenees/phpgpxparser.svg)](https://packagist.org/packages/inspyrenees/umami-bundle)
[![Packagist downloads](https://img.shields.io/packagist/dm/inspyrenees/phpgpxparser.svg)](https://packagist.org/packages/inspyrenees/umami-bundle)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![Symfony Version](https://img.shields.io/badge/symfony-%5E7.0%7C%5E8.0-blue.svg)](https://symfony.com)

A Symfony bundle for integrating with [Umami Analytics](https://umami.is/) API. This bundle provides a simple and elegant way to fetch analytics data from your Umami instance.

## Features

- 🔐 Automatic authentication with Umami API
- 📊 Complete API coverage (metrics, stats, page views, etc.)
- ⚙️ Simple configuration via environment variables
- 🎯 Type-safe and well-documented methods
- 🔄 Token caching to minimize authentication requests

## Requirements

- PHP 8.1 or higher
- Symfony 7.0 or 8.0
- Umami Analytics instance with API access

## Installation

Install the bundle via Composer:

```bash
composer require inspyrenees/umami-bundle
```

If you're not using Symfony Flex, you'll need to enable the bundle manually in `config/bundles.php`:

```php
<?php

return [
    // ...
    Inspyrenees\UmamiBundle\UmamiBundle::class => ['all' => true],
];
```

## Configuration

### Simple Setup (Recommended)

Add your Umami credentials directly to your `.env` file:

```env
UMAMI_URL=https://analytics.example.com
UMAMI_USERNAME=your_username
UMAMI_PASSWORD=your_password
UMAMI_WEBSITE_ID=your-website-id
```

That's it! The bundle will automatically use these environment variables.

### Advanced Configuration (Optional)

If you need to customize settings or override environment variables, create `config/packages/umami.yaml`:

```yaml
umami:
    url: 'https://custom-analytics.example.com'  # Override UMAMI_URL
    username: '%env(UMAMI_USERNAME)%'
    password: '%env(UMAMI_PASSWORD)%'
    website_id: '%env(UMAMI_WEBSITE_ID)%'
    default_days_back: 90  # Change default from 30 to 90 days
```

## Usage

### Basic Usage

Inject the `UmamiClientInterface` into your controller or service:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Inspyrenees\UmamiBundle\Client\UmamiClientInterface;

class AnalyticsController extends AbstractController
{
    public function __construct(
        private readonly UmamiClientInterface $umamiClient
    ) {}

    public function dashboard(): Response
    {
        // Get page metrics for the last 30 days (default)
        $metrics = $this->umamiClient->getPageMetrics();
        
        // Get overall stats
        $stats = $this->umamiClient->getStats();
        
        return $this->render('analytics/dashboard.html.twig', [
            'metrics' => $metrics,
            'stats' => $stats,
        ]);
    }
}
```

### Available Methods

#### Get Page Metrics

```php
// Get metrics for the last 30 days (default)
$metrics = $this->umamiClient->getPageMetrics();

// Get metrics for the last 7 days
$metrics = $this->umamiClient->getPageMetrics(7);
```

#### Get Website Statistics

```php
// Get overall statistics
$stats = $this->umamiClient->getStats(30);
```

Returns:
```json
{
  "pageviews": 5000,
  "visitors": 2000,
  "visits": 2500,
  "bounces": 500,
  "totaltime": 150000
}
```

#### Get Metrics by Type

```php
// Get referrer data
$referrers = $this->umamiClient->getReferrers(30);

// Get browser data
$browsers = $this->umamiClient->getBrowsers(30);

// Get operating system data
$os = $this->umamiClient->getOperatingSystems(30);

// Get device data
$devices = $this->umamiClient->getDevices(30);

// Get country data
$countries = $this->umamiClient->getCountries(30);

// Get event data
$events = $this->umamiClient->getEvents(30);
```

Example response format:
```json
[
    { "x": "/home", "y": 1523 },
    { "x": "/about", "y": 456 }
]
```

#### Get Page Views Over Time

```php
// Get daily page views for the last 30 days
$pageViews = $this->umamiClient->getPageViews('day', 'Europe/Paris', 30);

// Get hourly views for the last 7 days
$hourlyViews = $this->umamiClient->getPageViews('hour', 'UTC', 7);
```

Available units: `year`, `month`, `day`, `hour`

#### Get Active Users (Real-time)

```php
// Get currently active users on your site
$activeUsers = $this->umamiClient->getActiveUsers();
```

### Advanced Usage

#### Custom Metrics Query

You can query any metric type supported by Umami:

```php
// Available types: url, referrer, browser, os, device, country, event
$customMetrics = $this->umamiClient->getMetricsByType('url', 15);
```

#### Clear Authentication Token

If you need to force re-authentication (e.g., after credential changes):

```php
$this->umamiClient->clearToken();
```


## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This bundle is released under the MIT License. See the [LICENSE](LICENSE) file for details.
