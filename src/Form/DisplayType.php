<?php

namespace App\Form;

use FervoEnumBundle\Generated\Form\DisplayTargetUserTypeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DisplayType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('targetUserType', DisplayTargetUserTypeType::class, [
                'label' => 'label.usertype',
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ]
            ])
            ->add('refreshTime', IntegerType::class, [
                'label' => 'label.refresh_time.label',
                'help' => 'label.refresh_time.help'
            ])
            ->add('scrollTime', IntegerType::class, [
                'label' => 'label.scroll_time.label',
                'help' => 'label.scroll_time.help'
            ])
            ->add('showDate', CheckboxType::class, [
                'label' => 'label.show.date',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('showTime', CheckboxType::class, [
                'label' => 'label.show.time',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('showInfotexts', CheckboxType::class, [
                'label' => 'label.show.infotexts',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('showAbsences', CheckboxType::class, [
                'label' => 'label.show.abesences',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('showWeek', CheckboxType::class, [
                'label' => 'label.show.week',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('showExams', CheckboxType::class, [
                'label' => 'label.show.exams',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}