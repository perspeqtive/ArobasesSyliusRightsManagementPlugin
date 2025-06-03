<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Access\Doctrine;

use Arobases\SyliusRightsManagementPlugin\Access\Listener\ChannelFilterConfiguratorInterface;
use Arobases\SyliusRightsManagementPlugin\Adapter\ChannelRightProvider;
use Arobases\SyliusRightsManagementPlugin\Entity\AdminUserInterface;
use Arobases\SyliusRightsManagementPlugin\Entity\Right;
use Arobases\SyliusRightsManagementPlugin\Provider\CurrentAdminUserProvider;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Model\Channel;

class ChannelFilterConfigurator implements ChannelFilterConfiguratorInterface
{

    public function __construct(
        private EntityManagerInterface     $entityManager,
        private CurrentAdminUserProvider   $currentAdminUserProvider,
    )
    {
    }

    public function configure(): void
    {
        $rights = $this->getCurrentUserRights();

        $channelNames = $this->getAllowedChannelNames($rights);
        $channelIds = $this->resolveIds($channelNames);
        $this->configureFilter($channelIds);
    }

    /**
     * @return Right[]
     */
    private function getCurrentUserRights(): array
    {
        /** @var AdminUserInterface $user */
        $user = $this->currentAdminUserProvider->getCurrentAdminUser();
        if($user instanceof AdminUserInterface === false) {
            return [];
        }
        $role = $user->getRole();
        return $role?->getRights()->toArray() ?? [];
    }

    /**
     * @param Right[] $rights
     * @return string[]
     */
    private function getAllowedChannelNames(array $rights): array
    {
        $channelNames = [];
        foreach ($rights as $right) {
            if ($right->getRightGroup()->getName() !== ChannelRightProvider::CHANNEL_RIGHT_GROUP_NAME) {
                continue;
            }
            $channelNames[] = $right->getName();
        }
        return $channelNames;
    }

    /**
     * @param int[] $channelIds
     */
    private function configureFilter(array $channelIds): void
    {
        /** @var ChannelFilter $filter */
        $filter = $this->entityManager->getFilters()->enable('channel_filter');
        $filter->setAllowedChannels($channelIds);
    }

    private function resolveIds(array $names): array
    {
        if ($names === []) {
            return [];
        }

        $qb = $this->entityManager->createQueryBuilder();

        $result = $qb
            ->select('c.id')
            ->from(Channel::class, 'c')
            ->where($qb->expr()->in('c.name', ':names'))
            ->setParameter('names', $names)
            ->getQuery()
            ->getScalarResult();

        return array_column($result, 'id');
    }

}