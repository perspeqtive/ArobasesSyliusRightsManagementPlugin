<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Tests\Unit\Mocks;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class MockHttpKernel implements HttpKernelInterface
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true)
    {

    }
}