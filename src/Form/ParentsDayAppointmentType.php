<?php

namespace App\Form;

use App\Entity\Teacher;
use App\Entity\User;
use App\Sorting\TeacherStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ParentsDayAppointmentType extends AbstractType {

    public function __construct(private readonly TeacherStrategy $teacherStrategy, private readonly TokenStorageInterface $tokenStorage) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('start', TimeType::class, [
                'label' => 'label.start',
                'widget' => 'single_text',
                'input' => 'datetime'
            ])
            ->add('end', TimeType::class, [
                'label' => 'label.end',
                'widget' => 'single_text',
                'input' => 'datetime'
            ])
            ->add('students', StudentsType::class, [
                'label' => 'label.students_simple',
                'multiple' => true,
                'required' => false
            ])
            ->add('teachers', SortableEntityType::class, [
                'class' => Teacher::class,
                'multiple' => true,
                'sort_by' => $this->teacherStrategy,
                'label' => 'label.teachers_simple',
                'attr' => [
                    'data-choice' => 'true'
                ]
            ]);

        $user = $this->tokenStorage->getToken()?->getUser();

        if(!$user instanceof User) {
            return;
        }

        if(!$user->isTeacher()) {
            $builder
                ->remove('start')
                ->remove('end')
                ->remove('teachers');
        }
    }
}