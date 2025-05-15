<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Checker;

interface AdminRouteCheckerInterface
{
    public function isAdminRoute(string $routeName): bool;
}
