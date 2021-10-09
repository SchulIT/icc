<?php

namespace App\Form\Import\Untis;

use App\Entity\TimetablePeriod;
use App\Form\SortableEntityType;
use App\Sorting\TimetablePeriodStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class SupervisionImportType extends AbstractType {

    private $periodStrategy;

    public function __construct(TimetablePeriodStrategy $periodStrategy) {
        $this->periodStrategy = $periodStrategy;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('period', SortableEntityType::class, [
                'label' => 'label.period',
                'placeholder' => 'label.choose',
                'sort_by' => $this->periodStrategy,
                'class' => TimetablePeriod::class,
                'choice_label' => function(TimetablePeriod $period) {
                    return $period->getName();
                }
            ])
            ->add('importFile', FileType::class, [
                'label' => 'GPU009.txt'
            ]);
    }
}