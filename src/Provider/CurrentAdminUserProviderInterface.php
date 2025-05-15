<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Provider;

use Symfony\Component\Security\Core\User\UserInterface;

interface CurrentAdminUserProviderInterface
{
    public function getCurrentAdminUser(): ?UserInterface;
}
