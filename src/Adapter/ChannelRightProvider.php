<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Adapter;

use Arobases\SyliusRightsManagementPlugin\Command\RightProviderInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

class ChannelRightProvider implements RightProviderInterface
{

    public const CHANNEL_RIGHT_GROUP_NAME = 'arobases_sylius_rights_management_plugin.rights.channel';

    public function __construct(
        private ChannelRepositoryInterface $channelRepository
    ) {}

    public function getRights(): ?array
    {
        $result = [];
        foreach($this->channelRepository->findAll() as $channel) {
            $result[self::CHANNEL_RIGHT_GROUP_NAME . '.' . $channel->getName()] = [
                'name' => $channel->getName(),
                'routes' => [],
                'excludes' => []
            ];
        }

        return [self::CHANNEL_RIGHT_GROUP_NAME => ['rights' => $result]];
    }
}