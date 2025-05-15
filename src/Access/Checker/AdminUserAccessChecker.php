<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Checker;

use Arobases\SyliusRightsManagementPlugin\Entity\Right;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminUserAccessChecker implements AdminUserAccessCheckerInterface
{
    private RouterInterface $router;

    private array $arrayListAllRoutes;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->arrayListAllRoutes = $this->router->getRouteCollection()->all();
    }

    public function isUserGranted(UserInterface $adminUser, string $routeName): bool
    {
        $authorizedRoutes = [];
        if (null === $adminUser->getRole()) {
            return false;
        }

        $rights = $adminUser->getRole()->getRights();
        /** @var Right $right */
        foreach ($rights as $right) {
            $authorizedRoutes = array_merge($this->getRightAuthorizedRoutes($right), $authorizedRoutes);
        }

        if (!empty($authorizedRoutes)) {
            if (in_array($routeName, $authorizedRoutes)) {
                return true;
            }
        }

        return false;
    }

    private function getRightAuthorizedRoutes(Right $right): array
    {
        $authorizedRoutes = [];
        $excludedRoutes = [];
        foreach ($right->getRoutes() as $route) {
            $authorizedRoutes = array_merge($this->resolveRoutes($route), $authorizedRoutes);
        }

        foreach ($right->getExcludedRoutes() as $toExclude) {
            $excludedRoutes = array_merge($this->resolveRoutes($toExclude), $excludedRoutes);
        }

        foreach ($authorizedRoutes as $key => $exclude) {
            if (in_array($exclude, $excludedRoutes)) {
                unset($authorizedRoutes[$key]);
            }
        }

        return $authorizedRoutes;
    }

    private function resolveRoutes(string $routeName): array
    {
        if (strpos($routeName, '*')) {
            $routeName = substr($routeName, 0, -2);
        }
        $routeName = '/^' . $routeName . '/';
        $routes = $this->arrayListAllRoutes;
        $authorizedRoutes = [];

        foreach ($routes as $route => $value) {
            if (preg_match($routeName, $route)) {
                $authorizedRoutes[] = $route;
            }
        }

        return $authorizedRoutes;
    }
}
