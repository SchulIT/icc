<?php

namespace App\Form;

use App\Entity\Subject;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FontAwesomeIconPicker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LessonAttendanceFlagType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('icon', FontAwesomeIconPicker::class, [
                'label' => 'label.icon.label',
                'help' => 'label.icon.help',
            ])
            ->add('stackIcon', FontAwesomeIconPicker::class, [
                'label' => 'label.stack_icon.label',
                'help' => 'label.stack_icon.help',
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => 'label.description'
            ])
            ->add('subjects', EntityType::class, [
                'label' => 'label.flag_subjects.label',
                'help' => 'label.flag_subjects.help',
                'required' => false,
                'class' => Subject::class,
                'multiple' => true,
                'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('s')->orderBy('s.name'),
                'choice_label' => fn(Subject $subject) => $subject->getName(),
                'attr' => [
                    'data-choice' => 'true'
                ],
            ]);
    }
}