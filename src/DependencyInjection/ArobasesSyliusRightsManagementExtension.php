<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\DependencyInjection;

use Arobases\SyliusRightsManagementPlugin\Access\Doctrine\ChannelFilter;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ArobasesSyliusRightsManagementExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $container->setParameter('arobases_sylius_rights_management', $config['groups']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('doctrine') === false) {
            return;
        }

        $filters = ['channel_filter' => [
            'class' => ChannelFilter::class,
            'enabled' => false,
        ]];
        $config = ['orm' => ['entity_managers' => ['default' => ['filters' =>$filters]]]];

        $container->prependExtensionConfig('doctrine', $config);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }


}