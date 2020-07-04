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
            ->add('substitutionsTarget', DisplayTargetUserTypeType::class, [
                'label' => 'label.substitutions'
            ])
            ->add('backgroundColor', ColorType::class, [
                'required' => false,
                'label' => 'label.background_color'
            ])
            ->add('maxNumberOfRows', IntegerType::class, [
                'label' => 'label.number_of_rows'
            ])
            ->add('fontFamily', TextType::class, [
                'required' => false,
                'label' => 'label.font_family'
            ])
            ->add('showDate', CheckboxType::class, [
                'label' => 'label.show.date',
                'required' => false
            ])
            ->add('showTime', CheckboxType::class, [
                'label' => 'label.show.time',
                'required' => false
            ])
            ->add('showInfotexts', CheckboxType::class, [
                'label' => 'label.show.infotexts',
                'required' => false
            ])
            ->add('showAbsences', CheckboxType::class, [
                'label' => 'label.show.abesences',
                'required' => false
            ])
            ->add('showWeek', CheckboxType::class, [
                'label' => 'label.show.week',
                'required' => false
            ])
            ->add('appointmentsTarget', DisplayTargetUserTypeType::class, [
                'required' => false,
                'placeholder' => 'label.empty',
                'label' => 'label.appointments_target'
            ]);
    }
}