<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NotificationsType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('email', TextType::class, [
                'disabled' => true,
                'label' => 'label.email'
            ])
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
    }
}