<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\DependencyInjection;

use Arobases\SyliusRightsManagementPlugin\Provider\CompositeRightProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RightProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(CompositeRightProvider::class)) {
            return;
        }

        $definition = $container->findDefinition(CompositeRightProvider::class);

        foreach ($container->findTaggedServiceIds('right_provider') as $id => $tags) {
            $definition->addMethodCall('register', [new Reference($id)]);
        }
    }
}