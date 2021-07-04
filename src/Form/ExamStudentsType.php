<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\StudentRepositoryInterface;
use App\Section\SectionResolver;
use App\Sorting\StringStrategy;
use App\Sorting\StudentStrategy;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ExamStudentsType extends AbstractType {

    private $studentConverter;
    private $stringStrategy;
    private $studentStrategy;
    private $studentRepository;
    private $sectionResolver;

    public function __construct(StudentStringConverter $studentConverter, StudentStrategy $studentStrategy,
                                StringStrategy $stringStrategy, StudentRepositoryInterface $studentRepository, SectionResolver $sectionResolver) {
        $this->studentConverter = $studentConverter;
        $this->stringStrategy = $stringStrategy;
        $this->studentStrategy = $studentStrategy;
        $this->studentRepository = $studentRepository;
        $this->sectionResolver = $sectionResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                /** @var Exam $exam */
                $exam = $event->getData();

                if($exam !== null) {
                    $studyGroups = $exam->getTuitions()->map(function(Tuition $tuition) {
                        return $tuition->getStudyGroup();
                    })->toArray();

                    $section = $this->sectionResolver->getSectionForDate($exam->getDate());

                    $form
                        ->add('students', SortableEntityType::class, [
                            'label' => 'label.students_simple',
                            'attr' => [
                                'size' => 15
                            ],
                            'class' => Student::class,
                            'multiple' => true,
                            'choice_label' => function(Student $student) {
                                return $this->studentConverter->convert($student);
                            },
                            'query_builder' => function(EntityRepository $repository) use($studyGroups) {
                                return $this->studentRepository
                                    ->getQueryBuilderFindAllByStudyGroups($studyGroups);
                            },
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