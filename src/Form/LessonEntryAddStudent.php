<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class LessonEntryAddStudent extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('csrf_field_name', '_token');
        $resolver->setDefault('csrf_token_id', 'lesson_entry_add_student');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', StudentsType::class, [
                'label' => 'label.student',
                'required' => true,
                'constraints' => [
                    new NotNull()
                ],
                'placeholder' => 'label.select.student',
                'multiple' => false,
                'choice_value' => function(?Student $student) {
                    if($student === null) {
                        return null;
                    }

                    return $student->getUuid()->toString();
                }
            ]);
    }
}