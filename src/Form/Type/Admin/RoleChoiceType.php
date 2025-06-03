<?php

declare(strict_types=1);

namespace Arobases\SyliusRightsManagementPlugin\Form\Type\Admin;

use Arobases\SyliusRightsManagementPlugin\Repository\Role\RoleRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class  RoleChoiceType extends AbstractType
{
    private RoleRepository $roleRepository;

    /**
     * RoleChoiceType constructor.
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => function (Options $options): array {
                return $this->roleRepository->findAll();
            },
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => true,
            'placeholder' => 'arobases_sylius_rights_management_plugin.ui.no_role',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'arobases_sylius_rights_management_plugin_role_choice';
    }
}