<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class BookEventType extends BookEventCreateType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        parent::buildForm($builder, $options);

        $builder
            ->add('date', DateType::class, [
                'label' => 'label.date',
                'widget' => 'single_text',
                'disabled' => true
            ])
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.start',
                'disabled' => true
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.end',
                'disabled' => true
            ])
            ->remove('students');

        $builder
            ->add('attendances', CollectionType::class, [
                'entry_type' => AttendanceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ]);
    }
}