<?php

namespace App\Form;

use App\Converter\UserTypeStringConverter;
use App\Entity\TimetablePeriodVisibility;
use App\Sorting\TimetablePeriodVisibilityStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TimetablePeriodType extends AbstractType {

    private $periodVisibilityStrategy;
    private $userTypeConverter;

    public function __construct(TimetablePeriodVisibilityStrategy $periodVisibilityStrategy, UserTypeStringConverter $userTypeConverter) {
        $this->periodVisibilityStrategy = $periodVisibilityStrategy;
        $this->userTypeConverter = $userTypeConverter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id'
            ])
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('start', DateType::class, [
                'label' => 'label.start'
            ])
            ->add('end', DateType::class, [
                'label' => 'label.end'
            ])
            ->add('visibilities', SortableEntityType::class, [
                'label' => 'label.visibility',
                'class' => TimetablePeriodVisibility::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function(TimetablePeriodVisibility $visibility) {
                    return $this->userTypeConverter->convert($visibility->getUserType());
                },
                'sort_by' => $this->periodVisibilityStrategy
            ]);
    }
}