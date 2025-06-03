<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Command;

use Arobases\SyliusRightsManagementPlugin\Adapter\RightAdapter;
use Arobases\SyliusRightsManagementPlugin\Entity\Right;
use Arobases\SyliusRightsManagementPlugin\Entity\RightGroup;
use Arobases\SyliusRightsManagementPlugin\Entity\Role;
use Arobases\SyliusRightsManagementPlugin\Repository\Group\RightGroupRepository;
use Arobases\SyliusRightsManagementPlugin\Repository\Right\RightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminRightCommand extends Command
{
    protected static $defaultName = 'arobases:right:create-admin-right';

    public function __construct(
        private EntityManagerInterface $manager,
        private RightProviderInterface $rightProvider,
        private RightGroupRepository   $groupRightRepository,
        private RightRepository        $rightRepository,
        private string                 $defaultAdminUser,
        private string                 $defaultAdminRoleCode,
        private string                 $defaultAdminRoleName
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $administratorRole = $this->getAdministratorRole();

        $arrayRights = $this->rightProvider->getRights();

        foreach ($arrayRights as $group => $values) {
            $rightGroup = $this->buildRightGroup($group);
            $this->buildRightGroupRights($values, $rightGroup, $administratorRole);
        }
        $this->manager->flush();

        return Command::SUCCESS;
    }

    private function getAdministratorRole(): ?Role
    {
        $administratorRole = null;
        $defaultAdminUser = $this->defaultAdminUser;
        $defaultAdminRoleCode = $this->defaultAdminRoleCode;
        $defaultAdminRoleName = $this->defaultAdminRoleName;
        if ($defaultAdminUser && $defaultAdminRoleCode && $defaultAdminRoleName) {
            $adminUser = $this->manager->getRepository(AdminUserInterface::class)->findOneBy(['username' => $defaultAdminUser]);
            if ($adminUser) {
                $administratorRole = $this->manager->getRepository(Role::class)->findOneBy(['code' => $defaultAdminRoleCode]);
                if (!$administratorRole) {
                    $administratorRole = new Role();
                    $administratorRole->setCode($defaultAdminRoleCode);
                    $administratorRole->setName($defaultAdminRoleName);
                }
                $adminUser->setRole($administratorRole);
            }
        }
        return $administratorRole;
    }

    private function buildRightGroup(string $group): RightGroup
    {
        /** @var RightGroup $rightGroup */
        $rightGroup = $this->groupRightRepository->findOneBy(['name' => $group]);
        if (!$rightGroup) {
            $rightGroup = new RightGroup();
        }
        $rightGroup->setName($group);
        $this->manager->persist($rightGroup);
        $this->manager->flush();
        return $rightGroup;
    }

    private function buildRightGroupRights(array $values, RightGroup $rightGroup, ?Role $administratorRole): void
    {
        if (!array_key_exists('rights', $values)) {
            return;
        }
        foreach ($values['rights'] as $value) {
            $right = $this->rightRepository->findOneBy(['name' => $value['name']]);
            if (!$right) {
                $right = new Right();
            }
            $right->setName($value['name']);
            $right->setRoutes($value['routes']);
            $right->setExcludedRoutes($value['excludes']);
            $right->setRightGroup($rightGroup);
            if ($administratorRole) {
                $right->addRole($administratorRole);
            }

            $this->manager->persist($right);
        }
    }
}