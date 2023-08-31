<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetableImportType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.start',
                'help' => 'import.timetable.help'
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.end',
                'help' => 'import.timetable.help'
            ])
            ->add('importFile', FileType::class, [
                'label' => 'lesson.txt'
            ]);
    }
}