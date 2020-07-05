<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\Tuition;
use App\Sorting\StringStrategy;
use App\Sorting\TuitionStrategy;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ExamType extends AbstractType {

    private $tuitionStrategy;
    private $stringStrategy;

    private $authorizationChecker;

    public function __construct(TuitionStrategy $tuitionStrategy, StringStrategy $stringStrategy, AuthorizationCheckerInterface $authorizationChecker) {
        $this->tuitionStrategy = $tuitionStrategy;
        $this->stringStrategy = $stringStrategy;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('date', DateType::class, [
                            'label' => 'label.date',
                            'widget' => 'single_text'
                        ])
                        ->add('lessonStart', IntegerType::class, [
                            'label' => 'label.start'
                        ])
                        ->add('lessonEnd', IntegerType::class, [
                            'label' => 'label.end'
                        ])
                        ->add('room', TextType::class, [
                            'label' => 'label.room',
                            'property_path' => 'rooms[0]',
                            'required' => false
                        ])
                        ->add('description', TextareaType::class, [
                            'label' => 'label.description',
                            'required' => false
                        ]);
                }
            ])
            ->add('group_tuitions', FieldsetType::class, [
                'legend' => 'label.tuitions',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('tuitions', SortableEntityType::class, [
                            'attr' => [
                                'size' => 10,
                                'disabled' => $this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR') !== true
                            ],
                            'multiple' => true,
                            'class' => Tuition::class,
                            'choice_label' => function(Tuition $tuition) {
                                return sprintf('%s - %s - %s', $tuition->getName(), $tuition->getStudyGroup()->getName(), $tuition->getSubject()->getName());
                            },
                            'group_by' => function(Tuition $tuition) {
                                return implode(', ', $tuition->getStudyGroup()->getGrades()->map(function(Grade $grade) { return $grade->getName(); })->toArray());
                            },
                            'sort_by' => $this->stringStrategy,
                            'sort_items_by' => $this->tuitionStrategy,
                            'disabled' => $this->authorizationChecker->isGranted('ROLE_EXAMS_CREATOR') !== true
                        ]);

                    $builder
                        ->add('addStudents', CheckboxType::class, [
                            'required' => false,
                            'mapped' => false,
                            'label' => 'admin.exams.students.add_all',
                            'help' => 'admin.exams.students.info',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                }
            ]);
    }
}