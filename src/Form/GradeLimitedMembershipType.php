<?php

namespace App\Form;

use App\Entity\GradeLimitedMembership;
use App\Entity\GradeMembership;
use App\Entity\Section;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeLimitedMembershipType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('student', EntityType::class, [
                'label' => 'label.student',
                'class' => Student::class
            ])
            ->add('section', EntityType::class, [
                'label' => 'label.section',
                'class' => Section::class
            ])
            ->add('until', DateType::class, [
                'label' => 'label.until',
                'widget' => 'single_text'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', GradeLimitedMembership::class);
    }
}