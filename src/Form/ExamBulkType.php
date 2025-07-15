<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Tuition;
use App\Section\SectionResolverInterface;
use App\Sorting\GradeNameStrategy;
use App\Sorting\SectionDateStrategy;
use App\Sorting\StringStrategy;
use App\Sorting\SubjectNameStrategy;
use App\Sorting\TuitionStrategy;
use App\Utils\ArrayUtils;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ExamBulkType extends AbstractType {

    public function __construct(private readonly GradeNameStrategy $gradeNameStrategy,
                                private readonly SubjectNameStrategy $subjectNameStrategy,
                                private readonly SectionDateStrategy $sectionDateStrategy)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('section', SortableEntityType::class, [
                'class' => Section::class,
                'choice_label' => fn(Section $section) => $section->getDisplayName(),
                'label' => 'admin.settings.general.current_section.label',
                'help' => 'admin.settings.general.current_section.help',
                'attr' => [
                    'data-choice' => 'true'
                ],
                'sort_by' => $this->sectionDateStrategy
            ])

            ->add('numberOfExams', IntegerType::class, [
                'label' => 'admin.exams.bulk.number_of_exams',
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0])
                ]
            ])
            ->add('subjects', SortableEntityType::class, [
                'class' => Subject::class,
                'label' => 'label.subjects',
                'choice_label' => fn(Subject $subject) => $subject->getName(),
                'multiple' => true,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'sort_by' => $this->subjectNameStrategy
            ])
            ->add('grades', SortableEntityType::class, [
                'class' => Grade::class,
                'label' => 'label.grades',
                'choice_label' => fn(Grade $grade) => $grade->getName(),
                'multiple' => true,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'sort_by' => $this->gradeNameStrategy
            ])
            ->add('addStudents', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.exams.students.add_all',
                'help' => 'admin.exams.students.info',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('canEdit', CheckboxType::class, [
                'label' => 'admin.exams.tuition_teachers_can_edit.label',
                'help' => 'admin.exams.tuition_teachers_can_edit.help',
                'required' => false
            ]);
    }
}