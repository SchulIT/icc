<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Student;
use App\Sorting\Sorter;
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
use Symfony\Component\OptionsResolver\OptionsResolver;

class SickNoteType extends AbstractType {

    private $studentConverter;
    private $sorter;
    private $dateHelper;

    public function __construct(StudentStringConverter $converter, Sorter $sorter, DateHelper $dateHelper) {
        $this->studentConverter = $converter;
        $this->sorter = $sorter;
        $this->dateHelper = $dateHelper;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('students');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('student', ChoiceType::class, [
                'choices' => $options['students'],
                'choice_label' => function(Student $student) {
                    return $this->studentConverter->convert($student);
                },
                'placeholder' => 'label.select.student',
                'attr' => [
                    'class' => 'custom-select'
                ],
                'label' => 'label.student'
            ])
            ->add('reason', SickNoteReasonType::class, [
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'label' => 'label.reason'
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