<?php

namespace App\ParentsDay\Form;

use App\Common\Converter\StudentStringConverter;
use App\ParentsDay\Entity\ParentsDayAppointment;
use App\Common\Entity\Student;
use App\Common\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookParentsDayAppointmentType extends AbstractType {

    public function __construct(private readonly StudentStringConverter $stringConverter) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('students', EntityType::class, [
                'label' => 'label.students_simple',
                'multiple' => true,
                'expanded' => true,
                'class' => Student::class,
                'choices' => $options['user']->getStudents(),
                'choice_label' => fn(Student $student) => $this->stringConverter->convert($student)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);
        $resolver->setDefault('data_class', ParentsDayAppointment::class);
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);
    }
}