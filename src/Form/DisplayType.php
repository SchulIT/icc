<?php

namespace App\Form;

use App\Entity\DisplayTargetUserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DisplayType extends AbstractType {

    public function __construct(private readonly TranslatorInterface $translator) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('targetUserType', EnumType::class, [
                'class' => DisplayTargetUserType::class,
                'label' => 'label.usertype',
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'choice_label' => function(DisplayTargetUserType $targetUserType) {
                    return $this->translator->trans('display_target_user_type.' . $targetUserType->value, [], 'enums');
                }
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
            ])
            ->add('countdownDate', DateType::class, [
                'label' => 'label.countdown.date.label',
                'help' => 'label.countdown.date.help',
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('countdownText', TextType::class, [
                'label' => 'label.countdown.text.label',
                'help' => 'label.countdown.text.help',
                'required' => false
            ])
        ;
    }
}