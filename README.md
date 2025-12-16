# Umami Bundle for Symfony

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![Symfony Version](https://img.shields.io/badge/symfony-%5E6.0%7C%5E7.0-blue.svg)](https://symfony.com)

A Symfony bundle for integrating with [Umami Analytics](https://umami.is/) API. This bundle provides a simple and elegant way to fetch analytics data from your Umami instance.

## Features

- 🔐 Automatic authentication with Umami API
- 📊 Complete API coverage (metrics, stats, page views, etc.)
- ⚙️ Simple configuration via Symfony config files
- 🎯 Type-safe and well-documented methods
- 🔄 Token caching to minimize authentication requests
- 🧪 Fully testable

## Requirements

- PHP 8.1 or higher
- Symfony 7.0/8.0
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

Create a configuration file `config/packages/umami.yaml`:

```yaml
umami:
    url: '%env(UMAMI_URL)%'
    username: '%env(UMAMI_USERNAME)%'
    password: '%env(UMAMI_PASSWORD)%'
    website_id: '%env(UMAMI_WEBSITE_ID)%'
    default_days_back: 30  # Optional, default: 30
```

Add your Umami credentials to your `.env` file:

```env
UMAMI_URL=https://analytics.example.com
UMAMI_USERNAME=your_username
UMAMI_PASSWORD=your_password
UMAMI_WEBSITE_ID=your-website-id
```

## Usage

### Basic Usage

Inject the `UmamiApiClient` service into your controller or service:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Inspyrenees\UmamiBundle\Client\UmamiApiClient;

class AnalyticsController extends AbstractController
{
    public function __construct(
        private readonly UmamiApiClient $umamiClient
    ) {}

    public function dashboard(): Response
    {
        // Get page metrics for the last 30 days
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
$stats = $this->umamiClient->getStats(30);
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

#### Get Page Views Over Time

```php
// Get daily page views for the last 30 days
$pageViews = $this->umamiClient->getPageViews('day', 'Europe/Paris', 30);

// Available units: 'year', 'month', 'day', 'hour'
$hourlyViews = $this->umamiClient->getPageViews('hour', 'UTC', 7);
```

#### Get Active Users (Real-time)

```php
$activeUsers = $this->umamiClient->getActiveUsers();
```

### Advanced Usage

#### Custom Metrics Query

```php
// Get metrics by custom type
$customMetrics = $this->umamiClient->getMetricsByType('path', 15);
```

#### Clear Authentication Token

If you need to force re-authentication (e.g., after credential changes):

```php
$this->umamiClient->clearToken();
```

## Example Response Formats

### Page Metrics Response

```json
[
    {
        "x": "/home",
        "y": 1523
    },
    {
        "x": "/about",
        "y": 456
    }
]
```

### Stats Response

```json
{
  "pageviews": 2,
  "visitors": 2,
  "visits": 2,
  "bounces": 2,
  "totaltime": 0,
  "comparison": {
    "pageviews": 0,
    "visitors": 0,
    "visits": 0,
    "bounces": 0,
    "totaltime": 0
  }
}
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

## Support

If you have any questions or issues, please open an issue on GitHub.
