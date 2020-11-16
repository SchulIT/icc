<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use App\Settings\SickNoteSettings;
use App\SickNote\SickNote;
use App\SickNote\SickNoteReason;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use FervoEnumBundle\Generated\Form\SickNoteReasonType;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SickNoteType extends AbstractType {

    private $studentConverter;
    private $studentStrategy;
    private $sorter;
    private $dateHelper;
    private $settings;

    public function __construct(StudentStringConverter $converter, StudentStrategy $strategy, Sorter $sorter, DateHelper $dateHelper, SickNoteSettings  $settings) {
        $this->studentConverter = $converter;
        $this->studentStrategy = $strategy;
        $this->sorter = $sorter;
        $this->dateHelper = $dateHelper;
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
            ->add('until', DateType::class, [
                'widget' => 'single_text',
                'label' => 'sick_notes.add.absent_until'
            ])
            ->add('message', TextareaType::class, [
                'label' => 'sick_notes.add.message',
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('attachments', FileType::class, [
                'multiple' => true,
                'label' => 'sick_notes.add.attachments.label',
                'required' => false
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