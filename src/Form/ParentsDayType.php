<?php

namespace App\Form;

use App\Entity\Grade;
use App\Sorting\GradeNameStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ParentsDayType extends AbstractType {

    public function __construct(private readonly GradeNameStrategy $gradeNameStrategy) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('title', TextType::class, [
                'label' => 'label.title'
            ])
            ->add('date', DateType::class, [
                'label' => 'label.date',
                'widget' => 'single_text'
            ])
            ->add('bookingAllowedFrom', DateType::class, [
                'label' => 'label.booking_allowed_from.label',
                'help' => 'label.booking_allowed_from.help',
                'widget' => 'single_text'
            ])
            ->add('bookingAllowedUntil', DateType::class, [
                'label' => 'label.booking_allowed_until.label',
                'help' => 'label.booking_allowed_until.help',
                'widget' => 'single_text'
            ])
            ->add('grades', SortableEntityType::class, [
                'label' => 'label.grades',
                'multiple' => true,
                'sort_by' => $this->gradeNameStrategy,
                'class' => Grade::class
            ]);
    }
}