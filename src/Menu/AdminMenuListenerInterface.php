<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

interface AdminMenuListenerInterface
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void;
}
