<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Tests\Unit\Mocks;

use Arobases\SyliusRightsManagementPlugin\Access\Listener\ChannelFilterConfiguratorInterface;

class MockChannelFilterConfigurator implements ChannelFilterConfiguratorInterface
{

    public bool $wasCalled = false;

    public function configure(): void
    {
        $this->wasCalled = true;
    }
}