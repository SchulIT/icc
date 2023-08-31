<?php

namespace App\Form;

use App\Entity\Week;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class WeekType extends EntityType {
    public function __construct(private readonly TranslatorInterface $translator, ManagerRegistry $registry) {
        parent::__construct($registry);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefault('class', Week::class);
        $resolver->setDefault('choice_label', fn(Week $week) => $this->translator->trans('date.week_label', [ '%week%' => $week->getNumber() ]));
        $resolver->setDefault('query_builder', fn(EntityRepository $repository) => $repository->createQueryBuilder('w')
            ->orderBy('w.number', 'asc'));
        $resolver->setDefault('by_reference', false);
    }
}