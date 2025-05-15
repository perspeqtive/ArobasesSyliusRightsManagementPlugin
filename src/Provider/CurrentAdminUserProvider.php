<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Provider;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentAdminUserProvider implements CurrentAdminUserProviderInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getCurrentAdminUser(): ?UserInterface
    {
        if (null === $this->tokenStorage->getToken()) {
            return null;
        }
        if (null === $this->tokenStorage->getToken()->getUser()) {
            return null;
        }

        return $this->tokenStorage->getToken()->getUser();
    }
}
