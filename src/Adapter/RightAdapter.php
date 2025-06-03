<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Adapter;

use Arobases\SyliusRightsManagementPlugin\Command\RightProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RightAdapter implements RightProviderInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getRights(): ?array
    {
        return $this->container->getParameter('arobases_sylius_rights_management');
    }
}