<?php

namespace App\Form;

use App\Converter\TeacherStringConverter;
use App\Entity\Teacher;
use App\Sorting\TeacherStrategy;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeacherChoiceType extends SortableEntityType {

    public function __construct(private TeacherStrategy $teacherStrategy, private TeacherStringConverter $teacherConverter, ManagerRegistry $registry) {
        parent::__construct($registry);
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'attr' => [
                'data-choice' => 'true'
            ],
            'class' => Teacher::class,
            'choice_label' => fn(Teacher $teacher) => $this->teacherConverter->convert($teacher, true),
            'sort_by' => $this->teacherStrategy
        ]);
    }
}