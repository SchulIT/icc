<?php

namespace App\Tools\WestermannZvs;

use App\Entity\LearningManagementSystem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class CheckRequestType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('json', TextareaType::class, [
                'label' => 'Schueler-JSON'
            ])
            ->add('lms', EntityType::class, [
                'label' => 'label.lms',
                'class' => LearningManagementSystem::class,
            ]);
    }
}