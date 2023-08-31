<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationsType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('allow_email', false)
            ->setDefault('allow_pushover', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('isSubstitutionNotificationsEnabled', CheckboxType::class, [
                'label' => 'profile.notifications.substitutions.label',
                'help' => 'profile.notifications.substitutions.help',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ],
                'required' => false
            ])
            ->add('isExamNotificationsEnabled', CheckboxType::class, [
                'label' => 'profile.notifications.exams.label',
                'help' => 'profile.notifications.exams.help',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ],
                'required' => false
            ])
            ->add('isMessageNotificationsEnabled', CheckboxType::class, [
                'label' => 'profile.notifications.messages.label',
                'help' => 'profile.notifications.messages.help',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ],
                'required' => false
            ]);

        if($options['allow_email']) {
            $builder
                ->add('email', TextType::class, [
                    'disabled' => true,
                    'required' => false,
                    'label' => 'profile.notifications.email.label_short'
                ])
                ->add('isEmailNotificationsEnabled', CheckboxType::class, [
                    'label' => 'profile.notifications.email.label',
                    'help' => 'profile.notifications.email.help',
                    'label_attr' => [
                        'class' => 'checkbox-custom'
                    ],
                    'required' => false
                ]);
        }

        if($options['allow_pushover']) {
            $builder
                ->add('pushoverToken', TextType::class, [
                    'required' => false,
                    'label' => 'profile.notifications.pushover.label',
                    'help' => 'profile.notifications.pushover.help'
                ]);
        }
    }
}