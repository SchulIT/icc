<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\StudentRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\StringStrategy;
use App\Sorting\StudentStrategy;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ExamStudentsType extends AbstractType {

    public function __construct(private StudentStringConverter $studentConverter, private StudentStrategy $studentStrategy, private StringStrategy $stringStrategy, private StudentRepositoryInterface $studentRepository, private SectionResolverInterface $sectionResolver)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                /** @var Exam $exam */
                $exam = $event->getData();

                if($exam !== null) {
                    $studyGroups = $exam->getTuitions()->map(fn(Tuition $tuition) => $tuition->getStudyGroup())->toArray();

                    if($exam->getDate() !== null) {
                        $section = $this->sectionResolver->getSectionForDate($exam->getDate());
                    } else {
                        $section = $this->sectionResolver->getCurrentSection();
                    }

                    $form
                        ->add('students', SortableEntityType::class, [
                            'label' => 'label.students_simple',
                            'attr' => [
                                'size' => 15
                            ],
                            'class' => Student::class,
                            'multiple' => true,
                            'choice_label' => fn(Student $student) => $this->studentConverter->convert($student),
                            'query_builder' => fn(EntityRepository $repository) => $this->studentRepository
                                ->getQueryBuilderFindAllByStudyGroups($studyGroups),
                            'group_by' => function(Student $student) use($section) {
                                $grade = $student->getGrade($section);

                                if($grade !== null) {
                                    return $grade->getName();
                                }

                                return '';
                            },
                            'sort_by' => $this->stringStrategy,
                            'sort_items_by' => $this->studentStrategy
                        ]);
                }
            });
    }
}