<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\Tuition;
use App\Section\SectionResolverInterface;
use App\Sorting\StringStrategy;
use App\Sorting\TuitionStrategy;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuitionChoiceType extends SortableEntityType {

    public function __construct(private readonly SectionResolverInterface $sectionResolver, private readonly StringStrategy $stringStrategy, private readonly TuitionStrategy $tuitionStrategy, ManagerRegistry $registry) {
        parent::__construct($registry);
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver->setDefault('class', Tuition::class);
        $resolver->setDefault('query_builder', function(EntityRepository $repository) {
            $section = $this->sectionResolver->getCurrentSection();

            $qb = $repository
                ->createQueryBuilder('t')
                ->select(['t', 's', 'sg', 'g']);

            if($section !== null) {
                $qb->leftJoin('t.section', 's')
                    ->leftJoin('t.studyGroup', 'sg')
                    ->leftJoin('sg.grades', 'g')
                    ->where('s.id = :section')
                    ->setParameter('section', $section->getId());
            }

            return $qb;
        });
        $resolver->setDefault('choice_label', function(Tuition $tuition) {
            if($tuition->getName() === $tuition->getStudyGroup()->getName()) {
                return sprintf('%s - %s', $tuition->getName(), $tuition->getSubject()->getName());
            }

            return sprintf('%s - %s - %s', $tuition->getName(), $tuition->getStudyGroup()->getName(), $tuition->getSubject()->getName());
        });
        $resolver->setDefault('group_by', fn(Tuition $tuition) => implode(', ', $tuition->getStudyGroup()->getGrades()->map(fn(Grade $grade) => $grade->getName())->toArray()));
        $resolver->setDefault('sort_by', $this->stringStrategy);
        $resolver->setDefault('sort_items_by', $this->tuitionStrategy);


    }
}