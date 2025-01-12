<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\ChecklistStudent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChecklistStudentType extends AbstractType {
    public function __construct(private readonly StudentStringConverter $studentStringConverter) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('isChecked', CheckboxType::class, [
                'required' => false,
                'label' => ' '
            ])
            ->add('comment', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'label.comment',
                    'class' => 'form-control-sm'
                ]
            ]);

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $checklistStudent = $event->getData();

                if($checklistStudent instanceof ChecklistStudent && $checklistStudent->getStudent() !== null) {
                    $form = $event->getForm();
                    $form->add('isChecked', CheckboxType::class, [
                        'required' => false,
                        'label' => $this->studentStringConverter->convert($checklistStudent->getStudent(), true)
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', ChecklistStudent::class);
    }
}