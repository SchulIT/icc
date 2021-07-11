<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use App\Section\SectionResolver;
use App\Sorting\StringStrategy;
use App\Sorting\StudentStrategy;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentsType extends SortableEntityType {

    private $studentConverter;
    private $stringStrategy;
    private $studentStrategy;
    private $sectionResolver;

    public function __construct(ManagerRegistry $registry, StudentStringConverter $studentConverter, StudentStrategy $studentStrategy,
                                StringStrategy $stringStrategy, SectionResolver $sectionResolver) {
        parent::__construct($registry);
        $this->studentConverter = $studentConverter;
        $this->stringStrategy = $stringStrategy;
        $this->studentStrategy = $studentStrategy;
        $this->sectionResolver = $sectionResolver;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $section = $this->sectionResolver->getCurrentSection();

        $resolver
            ->setDefaults([
                'attr' => [
                    'size' => 15,
                    'choices' => 'true'
                ],
                'class' => Student::class,
                'multiple' => true,
                'choice_label' => function(Student $student) {
                    return $this->studentConverter->convert($student);
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