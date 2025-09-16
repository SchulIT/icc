<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimetableHtmlAlternativeImportType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('weeks', [ ]);
    }

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
            ]);

        foreach($options['weeks'] as $week) {
            $builder
                ->add('grades_' . $week->getKey(), FileType::class, [
                    'label' => 'import.timetable.html_alternative.label_grades',
                    'label_translation_parameters' => [
                        '%week%' => $week->getDisplayName(),
                    ],
                    'help' => 'import.timetable.html.zip',
                    'required' => false
                ])
                ->add('subjects_' . $week->getKey(),FileType::class, [
                    'label' => 'import.timetable.html_alternative.label_subjects' ,
                    'label_translation_parameters' => [
                        '%week%' => $week->getDisplayName(),
                    ],
                    'help' => 'import.timetable.html.zip',
                    'required' => false
                ]);
        }
    }
}