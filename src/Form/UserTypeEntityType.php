<?php

namespace App\Form;

use App\Converter\EnumStringConverter;
use App\Entity\UserTypeEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTypeEntityType extends EntityType {

    public function __construct(ManagerRegistry $registry, private EnumStringConverter $enumStringConverter) {
        parent::__construct($registry);
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('class', UserTypeEntity::class)
            ->setDefault('query_builder', fn(EntityRepository $repository) => $repository->createQueryBuilder('v')
                ->orderBy('v.userType', 'asc'))
            ->setDefault('choice_label', fn(UserTypeEntity $visibility) => $this->enumStringConverter->convert($visibility->getUserType()));
    }
}