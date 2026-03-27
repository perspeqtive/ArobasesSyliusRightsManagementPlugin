<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Command;

use Arobases\SyliusRightsManagementPlugin\Adapter\ChannelRightProvider;
use Arobases\SyliusRightsManagementPlugin\Entity\Right;
use Arobases\SyliusRightsManagementPlugin\Entity\RightGroup;
use Arobases\SyliusRightsManagementPlugin\Repository\Group\RightGroupRepository;
use Arobases\SyliusRightsManagementPlugin\Repository\Right\RightRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'perspeqtive:update-channel-rights', description: 'Hello PhpStorm')]
class UpdateChannelRightsCommand extends Command
{

    protected static $defaultName = 'perspeqtive:update-channel-rights';

    public function __construct(
        private RightGroupRepository   $rightGroupRepository,
        private EntityManagerInterface $entityManager,
        private ChannelRightProvider   $channelRightProvider,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rightGroup = $this->getRightGroupForChannel();

        $this->addNewRights($rightGroup);
        $this->removeUnusedRights($rightGroup);

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    protected function getRightGroupForChannel(): RightGroup
    {
        try {
            $rightGroup = $this->rightGroupRepository->findOneBy(['name' => ChannelRightProvider::CHANNEL_RIGHT_GROUP_NAME]);
            if ($rightGroup instanceof RightGroup) {
                return $rightGroup;
            }
        } catch (\Exception) {

        }
        $rightGroup = new RightGroup();
        $rightGroup->setName(ChannelRightProvider::CHANNEL_RIGHT_GROUP_NAME);
        $this->entityManager->persist($rightGroup);
        $this->entityManager->flush();
        return $rightGroup;
    }

    private function addNewRights(RightGroup $rightGroup): void
    {
        $existingRights = $rightGroup->getRights();
        $rightsConfig = $this->channelRightProvider->getRights();
        if (!$rightsConfig) {
            return;
        }

        $newRights = $rightsConfig[ChannelRightProvider::CHANNEL_RIGHT_GROUP_NAME]['rights'];

        foreach ($newRights as $right) {
            if ($this->isExistingRight($existingRights, $right)) {
                continue;
            }

            $rightEntity = new Right();
            $rightEntity->setName($right['name']);
            $rightEntity->setRoutes($right['routes'] ?? []);
            $rightEntity->setExcludedRoutes($right['excludes'] ?? []);
            $rightEntity->setRightGroup($rightGroup);
            $this->entityManager->persist($rightEntity);
        }
    }

    private function removeUnusedRights(RightGroup $rightGroup): void
    {
        $existingRights = $rightGroup->getRights();
        $rightsConfig = $this->channelRightProvider->getRights();
        if (!$rightsConfig) {
            return;
        }

        $validRightNames = $rightsConfig[ChannelRightProvider::CHANNEL_RIGHT_GROUP_NAME]['rights'];

        foreach ($existingRights as $existingRight) {
            if (!isset($validRightNames[ChannelRightProvider::CHANNEL_RIGHT_GROUP_NAME .'.'. $existingRight->getName()])) {
                $this->entityManager->remove($existingRight);
            }
        }
    }

    private function isExistingRight(Collection $existingRights, array $right): bool
    {
        foreach ($existingRights as $existingRight) {
            if ($existingRight->getName() === $right['name']) {
                return true;
            }
        }
        return false;
    }
}
