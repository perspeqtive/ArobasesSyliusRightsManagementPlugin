<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Tests\Unit\Access\Listener;

use Arobases\SyliusRightsManagementPlugin\Access\Listener\DoctrineChannelFilterListener;
use Arobases\SyliusRightsManagementPlugin\Tests\Unit\Mocks\MockChannelFilterConfigurator;
use Arobases\SyliusRightsManagementPlugin\Tests\Unit\Mocks\MockHttpKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class DoctrineChannelFilterListenerTest extends TestCase
{

    private DoctrineChannelFilterListener $listener;
    private MockChannelFilterConfigurator $configurator;

    protected function setUp(): void
    {
        $this->configurator = new MockChannelFilterConfigurator();
        $this->listener = new DoctrineChannelFilterListener($this->configurator);
    }


    public function testOnKernelRequestDoesNotHandleSubRequest(): void {

        $request = new Request(server: ['REQUEST_URI' => '/admin/somewhere']);

        $event = new RequestEvent(new MockHttpKernel(), $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($this->configurator->wasCalled);
    }

    public function testOnKernelRequestDoesNotHandleAPIRequest(): void {

        $request = new Request(server: ['REQUEST_URI' => '/api/v2/somewhere']);

        $event = new RequestEvent(new MockHttpKernel(), $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($this->configurator->wasCalled);
    }

    public function testOnKernelRequestHandlesMainAdminRequest(): void {

        $request = new Request(server: ['REQUEST_URI' => '/admin/somewhere']);

        $event = new RequestEvent(new MockHttpKernel(), $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertTrue($this->configurator->wasCalled);
    }
}