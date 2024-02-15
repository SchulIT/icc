<?php

namespace App\Form;

use App\Entity\Section;
use App\Sorting\SectionDateStrategy;
use App\Tools\TuitionReport;
use App\Utils\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TuitionReportInputType extends AbstractType {
    public function __construct(private readonly TuitionReport $tuitionReport, private readonly SectionDateStrategy $sectionDateStrategy) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('section', SortableEntityType::class, [
                'label' => 'label.section',
                'sort_by' => $this->sectionDateStrategy,
                'class' => Section::class,
                'choice_label' => fn(Section $section) => $section->getDisplayName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('types', ChoiceType::class, [
                'label' => 'label.course_types',
                'multiple' => true,
                'expanded' => true,
                'choices' => ArrayUtils::createArrayWithKeys($this->tuitionReport->getMembershipTypes(), fn(string $type) => $type),
                'data' => $this->tuitionReport->getMembershipTypes(),
                'required' => false
            ]);
    }
}