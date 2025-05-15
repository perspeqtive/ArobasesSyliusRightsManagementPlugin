<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Checker;

class AdminRouteChecker implements AdminRouteCheckerInterface
{
    public function isAdminRoute(string $routeName): bool
    {
        if (strpos($routeName, 'admin') !== false) {
            return true;
        }

        return false;
    }
}
