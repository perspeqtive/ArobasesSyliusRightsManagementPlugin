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
        if ($targetEntity->reflClass->implementsInterface(ChannelAwareInterface::class)) {
            return sprintf('%s.channel_id IN (%s)',
                $targetTableAlias,
                $this->getAllowedChannels()
            );
        }

        if ($targetEntity->hasAssociation('order')) {
            $orderMapping = $targetEntity->getAssociationMapping('order');
            if (isset($orderMapping['targetEntity']) && is_subclass_of($orderMapping['targetEntity'], ChannelAwareInterface::class)) {
                $columnName = $orderMapping['joinColumns'][0]['name'] ?? 'order_id';

                return sprintf('%s.%s IN (SELECT id FROM sylius_order WHERE channel_id IN (%s))',
                    $targetTableAlias,
                    $columnName,
                    $this->getAllowedChannels()
                );
            }
        }

        return '';
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