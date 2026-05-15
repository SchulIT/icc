<?php

namespace App\TeacherAbsence\Form;

use App\Common\Converter\TeacherStringConverter;
use App\Common\Entity\Teacher;
use App\Common\Form\Type\DateLessonType;
use App\Common\Form\Type\MarkdownType;
use App\Common\Form\Type\SortableEntityType;
use App\TeacherAbsence\Entity\TeacherAbsence;
use App\TeacherAbsence\Entity\TeacherAbsenceType as TeacherAbsenceTypeEntity;
use App\Common\Sorting\TeacherStrategy;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TeacherAbsenceType extends AbstractType {

    public function __construct(private readonly TeacherStringConverter $teacherStringConverter, private readonly TeacherStrategy $teacherStrategy) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('teacher', SortableEntityType::class, [
                'label' => 'label.teacher',
                'class' => Teacher::class,
                'choice_label' => fn(Teacher $teacher) => $this->teacherStringConverter->convert($teacher),
                'sort_by' => $this->teacherStrategy,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'placeholder' => 'label.choose'
            ])
            ->add('from', DateLessonType::class, [
                'label' => 'label.from',
                'attr' => [
                    'data-correct-start-target' => '[from][date]',
                    'data-correct-end-target' => '[until][date]'
                ]
            ])
            ->add('until', DateLessonType::class, [
                'label' => 'label.until'
            ])
            ->add('type', EntityType::class, [
                'label' => 'label.type',
                'class' => TeacherAbsenceTypeEntity::class,
                'choice_label' => fn(TeacherAbsenceTypeEntity $type) => $type->getName(),
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
            ])
            ->add('message', MarkdownType::class, [
                'label' => 'label.message.label',
                'required' => false
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $absence = $event->getData();
                $form = $event->getForm();

                if(!$absence instanceof TeacherAbsence) {
                    return;
                }

                if($absence->getId() !== null) {
                    $form
                        ->add('from', DateLessonType::class, [
                            'label' => 'label.from',
                            'disabled' => true
                        ])
                        ->add('until', DateLessonType::class, [
                            'label' => 'label.until',
                            'disabled' => true
                        ]);
                }
            });
    }
}