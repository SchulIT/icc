<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\Section;
use App\Sorting\GradeNameStrategy;
use App\Sorting\SectionDateStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GradeResponsibilityType extends AbstractType {

    public function __construct(private readonly GradeNameStrategy $gradeNameStrategy, private readonly SectionDateStrategy $sectionDateStrategy) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('grade', SortableEntityType::class, [
                'label' => 'label.grade',
                'attr' => [
                    'size' => 10,
                    'data-choice' => 'true'
                ],
                'multiple' => false,
                'class' => Grade::class,
                'choice_label' => fn(Grade $grade) => $grade->getName(),
                'sort_by' => $this->gradeNameStrategy,
                'placeholder' => 'label.select.grade'
            ])
            ->add('section', SortableEntityType::class, [
                'label' => 'label.section',
                'attr' => [
                    'size' => 10,
                    'data-choice' => 'true'
                ],
                'multiple' => false,
                'class' => Section::class,
                'choice_label' => fn(Section $section) => $section->getDisplayName(),
                'sort_by' => $this->sectionDateStrategy,
                'placeholder' => 'label.select.grade'
            ])
            ->add('task', TextType::class, [
                'label' => 'label.task'
            ])
            ->add('person', TextType::class, [
                'label' => 'label.person'
            ]);
    }
}