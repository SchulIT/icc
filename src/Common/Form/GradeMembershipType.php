<?php

namespace App\Common\Form;

use App\Common\Entity\GradeMembership;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeMembershipType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
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

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', GradeMembership::class);
    }
}