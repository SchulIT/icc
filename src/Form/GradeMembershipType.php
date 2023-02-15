<?php

namespace App\Form;

use App\Entity\GradeMembership;
use App\Entity\Section;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeMembershipType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', EntityType::class, [
                'label' => 'label.student',
                'class' => Student::class
            ])
            ->add('section', EntityType::class, [
                'label' => 'label.section',
                'class' => Section::class
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', GradeMembership::class);
    }
}