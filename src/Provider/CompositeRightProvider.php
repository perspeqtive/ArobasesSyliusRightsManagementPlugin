<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Provider;

use Arobases\SyliusRightsManagementPlugin\Command\RightProviderInterface;

class CompositeRightProvider implements RightProviderInterface
{

    /**
     * @var RightProviderInterface[]
     */
    private array $provider = [];

    public function register(RightProviderInterface $provider): void {
        $this->provider[] = $provider;
    }


    public function getRights(): ?array
    {
        $allRights = [];
        foreach($this->provider as $provider) {
            $allRights[] = $provider->getRights();
        }
        return array_merge(...$allRights);
    }
}