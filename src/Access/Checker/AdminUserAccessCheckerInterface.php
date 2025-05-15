<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Checker;

use Symfony\Component\Security\Core\User\UserInterface;

interface AdminUserAccessCheckerInterface
{
    public function isUserGranted(UserInterface $adminUser, string $routeName): bool;
}
