<?php

use Inspyrenees\UmamiBundle\Client\TimeRangeResolver;
use Inspyrenees\UmamiBundle\Client\UmamiApiClient;
use Inspyrenees\UmamiBundle\Client\UmamiClientInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->set(UmamiApiClient::class)
        ->autowire()
        ->autoconfigure()
        ->private()
        ->arg('$umamiUrl', '%umami.url%')
        ->arg('$umamiUsername', '%umami.username%')
        ->arg('$umamiPassword', '%umami.password%')
        ->arg('$websiteId', '%umami.website_id%')
        ->arg('$defaultDaysBack', '%umami.default_days_back%')
    ;

    $services
        ->set(TimeRangeResolver::class)
        ->private()
    ;

    $services
        ->alias(UmamiClientInterface::class, UmamiApiClient::class)
        ->public();
};
