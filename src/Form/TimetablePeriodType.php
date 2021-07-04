<?php

namespace App\Form;

use App\Entity\Section;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetablePeriodType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id'
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('start', DateType::class, [
                'label' => 'label.start',
                'widget' => 'single_text'
            ])
            ->add('end', DateType::class, [
                'label' => 'label.end',
                'widget' => 'single_text'
            ])
            ->add('section', EntityType::class, [
                'label' => 'label.section',
                'class' => Section::class,
                'choice_label' => function(Section $section) {
                    return $section->getDisplayName();
                }
            ])
            ->add('visibilities', UserTypeEntityType::class, [
                'label' => 'label.visibility',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}