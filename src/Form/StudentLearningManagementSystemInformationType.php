<?php

namespace App\Form;

use App\Entity\LearningManagementSystem;
use App\Entity\StudentLearningManagementSystemInformation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentLearningManagementSystemInformationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('lms', EntityType::class, [
                'label' => 'label.lms',
                'class' => LearningManagementSystem::class,
                'choice_label' => fn(LearningManagementSystem $lms) => $lms->getName(),
            ])
            ->add('username', TextType::class, [
                'label' => 'label.username',
                'required' => false
            ])
            ->add('password', TextType::class, [
                'label' => 'label.password',
                'required' => false
            ])
            ->add('isConsented', CheckboxType::class, [
                'label' => 'label.consented',
                'required' => false
            ])
            ->add('isConsentObtained', CheckboxType::class, [
                'label' => 'label.consent_obtained',
                'required' => false
            ])
            ->add('isAudioConsented', CheckboxType::class, [
                'label' => 'label.audio_consented',
                'required' => false
            ])
            ->add('isVideoConsented', CheckboxType::class, [
                'label' => 'label.video_consented',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', StudentLearningManagementSystemInformation::class);
    }
}