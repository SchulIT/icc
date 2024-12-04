<?php

namespace App\Tools\SubstitutionEvaluation;

use App\Utils\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class ReportInputType extends AbstractType {

    public function __construct(private readonly ReportManager $reportManager) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $substitutionTypes = $this->reportManager->getSubstitutionTypes();

        $builder
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.start'
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.end'
            ])
            ->add('substitutionTypes', ChoiceType::class, [
                'label' => 'label.substitution_types',
                'multiple' => true,
                'expanded' => true,
                'choices' => ArrayUtils::createArrayWithKeysAndValues(
                    $substitutionTypes,
                    fn(string $type) => $type,
                    fn(string $type) => $type
                )
            ]);
    }
}