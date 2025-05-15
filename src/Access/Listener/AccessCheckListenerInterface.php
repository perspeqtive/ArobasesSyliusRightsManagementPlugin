<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Listener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

interface AccessCheckListenerInterface
{
    public function onKernelRequest(RequestEvent $event): void;
}
