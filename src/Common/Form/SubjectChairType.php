<?php

namespace App\Common\Form;

use App\Common\Entity\ChairType;
use App\Common\Entity\Subject;
use App\Common\Entity\SubjectChair;
use App\Common\Entity\Teacher;
use App\Common\Form\Choice\TeacherChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubjectChairType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('hide_subject', true);
        $resolver->setDefault('hide_teacher', false);
        $resolver->setDefault('data_class', SubjectChair::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        if($options['hide_subject'] !== true) {
            $builder
                ->add('subject', EntityType::class, [
                    'class' => Subject::class,
                    'choice_label' => fn(Subject $subject) => $subject->getName(),
                    'placeholder' => 'label.select.subject',
                    'attr' => [
                        'data-choice' => 'true'
                    ]
                ]);
        }
        if($options['hide_teacher'] !== true) {
            $builder
                ->add('teacher', TeacherChoiceType::class, [
                    'placeholder' => 'label.select.teacher'
                ]);
        }

        $builder
            ->add('chairType', EnumType::class, [
                'class' => ChairType::class,
            ]);
    }
}
