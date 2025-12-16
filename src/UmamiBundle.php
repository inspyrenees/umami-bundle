<?php

namespace Inspyrenees\UmamiBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class UmamiBundle extends AbstractBundle
{
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder
    ): void {
        $builder->setParameter('umami.url', $config['url']);
        $builder->setParameter('umami.username', $config['username']);
        $builder->setParameter('umami.password', $config['password']);
        $builder->setParameter('umami.website_id', $config['website_id']);
        $builder->setParameter('umami.default_days_back', $config['default_days_back']);
    }

    public function getPath(): string
    {
        return __DIR__;
    }
}
