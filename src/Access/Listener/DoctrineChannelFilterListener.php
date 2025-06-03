<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Listener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class DoctrineChannelFilterListener
{

    public function __construct(private ChannelFilterConfiguratorInterface $configurator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if($this->isAdminRequest($event) === false) {
            return;
        }
        $this->configurator->configure();
    }

    private function isAdminRequest(RequestEvent $event): bool
    {
        if (!$event->isMainRequest()) {
            return false;
        }

        if (str_starts_with($event->getRequest()->getRequestUri(), '/admin') === false) {
            return false;
        }
        return true;
    }


}