<?php

namespace App\Form;

use App\Converter\TeacherStringConverter;
use App\Entity\Teacher;
use App\Sorting\TeacherStrategy;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeacherChoiceType extends SortableEntityType {

    private TeacherStrategy $teacherStrategy;
    private TeacherStringConverter $teacherConverter;

    public function __construct(TeacherStrategy $teacherStrategy, TeacherStringConverter $teacherConverter, ManagerRegistry $registry) {
        parent::__construct($registry);

        $this->teacherStrategy = $teacherStrategy;
        $this->teacherConverter = $teacherConverter;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'attr' => [
                'data-choice' => 'true'
            ],
            'class' => Teacher::class,
            'choice_label' => function(Teacher $teacher) {
                return $this->teacherConverter->convert($teacher);
            },
            'sort_by' => $this->teacherStrategy
        ]);
    }
}