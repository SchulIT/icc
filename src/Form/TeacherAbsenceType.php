<?php

namespace App\Form;

use App\Converter\TeacherStringConverter;
use App\Entity\Teacher;
use App\Entity\TeacherAbsence;
use App\Sorting\TeacherStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TeacherAbsenceType extends AbstractType {

    public function __construct(private readonly TeacherStringConverter $teacherStringConverter, private readonly TeacherStrategy $teacherStrategy) { }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('teacher', SortableEntityType::class, [
                'label' => 'label.teacher',
                'class' => Teacher::class,
                'choice_label' => fn(Teacher $teacher) => $this->teacherStringConverter->convert($teacher),
                'sort_by' => $this->teacherStrategy
            ])
            ->add('from', DateLessonType::class, [
                'label' => 'label.from'
            ])
            ->add('until', DateLessonType::class, [
                'label' => 'label.until'
            ])
            ->add('reason', TextType::class, [
                'label' => 'label.reason'
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