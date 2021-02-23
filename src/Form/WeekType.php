<?php

namespace App\Form;

use App\Entity\Week;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class WeekType extends EntityType {
    private $translator;

    public function __construct(TranslatorInterface $translator, ManagerRegistry $registry) {
        parent::__construct($registry);
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver->setDefault('class', Week::class);
        $resolver->setDefault('choice_label', function(Week $week) {
            return $this->translator->trans('date.week_label', [ '%week%' => $week->getNumber() ]);
        });
        $resolver->setDefault('query_builder', function (EntityRepository $repository) {
            return $repository->createQueryBuilder('w')
                ->orderBy('w.number', 'asc');
        });
    }
}