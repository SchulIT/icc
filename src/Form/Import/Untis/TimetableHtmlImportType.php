<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetableHtmlImportType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
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
            ->add('grades', FileType::class, [
                'label' => 'import.timetable.html.label_grades',
                'help' => 'import.timetable.html.zip',
                'required' => false
            ])
            ->add('subjects', FileType::class, [
                'label' => 'import.timetable.html.label_subjects',
                'help' => 'import.timetable.html.zip',
                'required' => false
            ]);
    }
}