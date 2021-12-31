<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\SickNote;
use App\Entity\SickNoteReason;
use App\Entity\Student;
use App\Settings\SickNoteSettings;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use FervoEnumBundle\Generated\Form\SickNoteReasonType;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SickNoteType extends AbstractType {

    private StudentStringConverter $studentConverter;
    private StudentStrategy $studentStrategy;
    private SickNoteSettings $settings;

    public function __construct(StudentStringConverter $converter, StudentStrategy $strategy, SickNoteSettings  $settings) {
        $this->studentConverter = $converter;
        $this->studentStrategy = $strategy;
        $this->settings = $settings;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('students');
        $resolver->setDefault('validation_groups', function(FormInterface $form) {
            /** @var SickNote $note */
            $note = $form->getData();

            if($note->getReason()->equals(SickNoteReason::Quarantine())) {
                return ['Default', 'quarantine'];
            }

            return ['Default'];
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', SortableChoiceType::class, [
                'choices' => $options['students'],
                'choice_label' => function(Student $student) {
                    return $this->studentConverter->convert($student, true);
                },
                'placeholder' => 'label.select.student',
                'attr' => [
                    'data-choice' => 'true',
                    'class' => 'custom-select'
                ],
                'sort_by' => $this->studentStrategy,
                'label' => 'label.student'
            ])
            ->add('reason', SickNoteReasonType::class, [
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'label' => 'label.reason'
            ])
            ->add('orderedBy', TextType::class, [
                'label' => 'label.ordered_by',
                'help' => $this->settings->getOrderedByHelp()
            ])
            ->add('from', DateLessonType::class, [
                'label' => 'sick_notes.add.absent_from'
            ])
            ->add('until', DateLessonType::class, [
                'label' => 'sick_notes.add.absent_until'
            ])
            ->add('message', TextareaType::class, [
                'label' => 'sick_notes.add.message',
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('attachments', CollectionType::class, [
                'entry_type' => SickNoteAttachmentType::class,
                'allow_add' => true,
                'allow_delete' => false,
                'by_reference' => false
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'sick_notes.add.phone'
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'label.email'
            ]);
    }
}