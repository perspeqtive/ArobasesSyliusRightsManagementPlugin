<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Sylius\Component\Channel\Model\ChannelAwareInterface;

class ChannelFilter extends SQLFilter
{
    private array $allowedChannels = [];

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!$targetEntity->reflClass->implementsInterface(ChannelAwareInterface::class)) {
            return '';
        }

        return sprintf('%s.channel_id IN (%s)',
            $targetTableAlias,
            $this->getAllowedChannels()
        );
    }

    public function setAllowedChannels(array $channels = []): void {
        $this->allowedChannels = $channels;
    }

    private function getAllowedChannels(): string {
        if($this->allowedChannels === []) {
            return '0';
        }

        return implode(',', $this->allowedChannels);
    }
}