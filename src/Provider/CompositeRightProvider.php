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
            $rights = $provider->getRights();
            foreach($rights as $name => $right) {
                if(!isset($allRights[$name])) {
                    $allRights[$name] = [];
                }
                $allRights[$name][] = $right;
            }
            $allRights[] = $rights;
        }
        return array_merge(...$allRights);
    }
}