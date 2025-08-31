<?php

namespace App\Import\External\Bildungslogin;

use App\Entity\LearningManagementSystem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportRequestType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('lms', EntityType::class, [
                'label' => 'label.lms',
                'class' => LearningManagementSystem::class,
                'choice_label' => fn(LearningManagementSystem $lms) => $lms->getName(),
            ])
            ->add('summaryCsv', FileType::class, [
                'label' => 'import.bilo.csv.summary.label',
                'help' => 'import.bilo.csv.summary.help',
            ])
            ->add('passwordsCsv', FileType::class, [
                'label' => 'import.bilo.csv.password.label',
                'help' => 'import.bilo.csv.password.help',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'actions.save',
            ]);
    }
}