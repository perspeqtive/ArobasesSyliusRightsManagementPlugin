<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Command;

interface RightProviderInterface
{

    public function getRights(): ?array;

}