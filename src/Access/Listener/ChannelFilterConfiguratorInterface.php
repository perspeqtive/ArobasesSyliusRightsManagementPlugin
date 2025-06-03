<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Listener;

interface ChannelFilterConfiguratorInterface
{
    public function configure(): void;
}