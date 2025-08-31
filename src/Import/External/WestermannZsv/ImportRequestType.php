<?php

namespace App\Import\External\WestermannZsv;

use App\Entity\LearningManagementSystem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportRequestType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('lms', EntityType::class, [
                'label' => 'label.lms',
                'class' => LearningManagementSystem::class,
                'choice_label' => fn(LearningManagementSystem $lms) => $lms->getName(),
            ])
            ->add('csv', FileType::class, [
                'label' => 'import.westermann_zvs.csv.label',
                'help' => 'import.westermann_zvs.csv.help',
            ])
            ->add('delimiter', TextType::class, [
                'label' => 'label.delimiter'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'actions.save',
            ]);
    }
}