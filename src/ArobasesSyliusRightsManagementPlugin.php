<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin;

use Arobases\SyliusRightsManagementPlugin\DependencyInjection\RightProviderCompilerPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ArobasesSyliusRightsManagementPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RightProviderCompilerPass());
    }

}