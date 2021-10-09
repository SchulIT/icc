<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class CalendarWeekSchoolWeekType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('calendar_week', IntegerType::class, [
                'label' => 'label.calendar_week'
            ])
            ->add('school_week', IntegerType::class, [
                'label' => 'label.school_week'
            ]);
    }
}