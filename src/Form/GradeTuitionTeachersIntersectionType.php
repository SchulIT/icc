<?php

namespace App\Form;

use App\Entity\Section;
use App\Entity\Subject;
use App\Sorting\SectionDateStrategy;
use App\Sorting\SubjectNameStrategy;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GradeTuitionTeachersIntersectionType extends AbstractType {

    public function __construct(private readonly SubjectNameStrategy $subjectNameStrategy, private readonly SectionDateStrategy $sectionDateStrategy) {}

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('leftGrades', GradeChoiceType::class, [
                'label' => 'tools.grade_teacher_intersection.left_grades',
                'multiple' => true
            ])
            ->add('rightGrades', GradeChoiceType::class, [
                'label' => 'tools.grade_teacher_intersection.right_grades',
                'multiple' => true
            ])
            ->add('subjects', SortableEntityType::class, [
                'label' => 'label.subjects',
                'sort_by' => $this->subjectNameStrategy,
                'class' => Subject::class,
                'choice_label' => fn(Subject $subject) => sprintf('%s (%s)', $subject->getName(), $subject->getAbbreviation()),
                'attr' => [
                    'data-choice' => 'true'
                ],
                'multiple' => true
            ])
            ->add('section', SortableEntityType::class, [
                'label' => 'label.section',
                'sort_by' => $this->sectionDateStrategy,
                'class' => Section::class,
                'choice_label' => fn(Section $section) => $section->getDisplayName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ]);
    }
}