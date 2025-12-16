<?php

namespace Inspyrenees\UmamiBundle;

use Inspyrenees\UmamiBundle\DependencyInjection\UmamiExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class UmamiBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new UmamiExtension();
    }

    public function getPath(): string
    {
        return __DIR__;
    }
}
