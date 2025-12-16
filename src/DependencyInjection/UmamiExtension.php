<?php

namespace Inspyrenees\UmamiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class UmamiExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('umami.url', $config['url']);
        $container->setParameter('umami.username', $config['username']);
        $container->setParameter('umami.password', $config['password']);
        $container->setParameter('umami.website_id', $config['website_id']);
        $container->setParameter('umami.default_days_back', $config['default_days_back']);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');
    }

    public function getAlias(): string
    {
        return 'umami';
    }
}
