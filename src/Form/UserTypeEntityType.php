<?php

namespace App\Form;

use App\Converter\EnumStringConverter;
use App\Entity\UserTypeEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTypeEntityType extends EntityType {

    private $enumStringConverter;

    public function __construct(ManagerRegistry $registry, EnumStringConverter $enumStringConverter) {
        parent::__construct($registry);

        $this->enumStringConverter = $enumStringConverter;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('class', UserTypeEntity::class)
            ->setDefault('query_builder', function(EntityRepository $repository) {
                return $repository->createQueryBuilder('v')
                    ->orderBy('v.userType', 'asc');
            })
            ->setDefault('choice_label', function(UserTypeEntity $visibility) {
                return $this->enumStringConverter->convert($visibility->getUserType());
            });
    }
}