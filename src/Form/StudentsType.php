<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use App\Section\SectionResolverInterface;
use App\Sorting\StringStrategy;
use App\Sorting\StudentStrategy;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentsType extends SortableEntityType {

    private $studentConverter;
    private $stringStrategy;
    private $studentStrategy;
    private $sectionResolver;

    public function __construct(ManagerRegistry $registry, StudentStringConverter $studentConverter, StudentStrategy $studentStrategy,
                                StringStrategy $stringStrategy, SectionResolverInterface $sectionResolver) {
        parent::__construct($registry);
        $this->studentConverter = $studentConverter;
        $this->stringStrategy = $stringStrategy;
        $this->studentStrategy = $studentStrategy;
        $this->sectionResolver = $sectionResolver;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $section = $this->sectionResolver->getCurrentSection();

        $resolver
            ->setDefaults([
                'attr' => [
                    'size' => 15,
                    'data-choice' => 'true'
                ],
                'class' => Student::class,
                'multiple' => true,
                'choice_label' => function(Student $student) use($section) {
                    return $this->studentConverter->convert($student, true, $section);
                },
                'query_builder' => function(EntityRepository $repository) use($section) {
                    $qb = $repository
                        ->createQueryBuilder('s')
                        ->select(['s', 'm', 'g'])
                        ->leftJoin('s.gradeMemberships', 'm')
                        ->leftJoin('m.grade', 'g');

                    if($section !== null) {
                        $qb->leftJoin('s.sections', 'sec')
                            ->where('sec.id = :section')
                            ->setParameter('section', $section->getId());
                    }

                    return $qb;
                },
                'group_by' => function(Student $student) use($section) {
                    $grade = $student->getGrade($section);

                    if($grade !== null) {
                        return $grade->getName();
                    }

                    return '';
                },
                'sort_by' => $this->stringStrategy,
                'sort_items_by' => $this->studentStrategy
            ]);
    }
}