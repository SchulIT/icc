<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ExcuseType extends AbstractType {

    private $studentConverter;

    public function __construct(StudentStringConverter $studentConverter) {
        $this->studentConverter = $studentConverter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => function(Student $student) {
                    return $this->studentConverter->convert($student);
                },
                'label' => 'label.student',
                'attr' => [
                    'data-choice' => 'true'
                ],
                'placeholder' => 'label.select.student.label'
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.date'
            ])
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.lesson_start'
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.lesson_end'
            ])
            ->add('comment', MarkdownType::class, [
                'label' => 'label.comment'
            ]);
    }
}