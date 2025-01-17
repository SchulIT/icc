<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChecklistType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('title', TextType::class, [
                'label' => 'label.title'
            ])
            ->add('description', MarkdownType::class, [
                'label' => 'label.description',
                'required' => false
            ])
            ->add('dueDate', DateType::class, [
                'label' => 'label.due_date.label',
                'help' => 'label.due_date.help',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('canStudentsView', CheckboxType::class, [
                'required' => false,
                'label' => 'label.can_students_view.label',
                'help' => 'label.can_students_view.help'
            ])
            ->add('canParentsView', CheckboxType::class, [
                'required' => false,
                'label' => 'label.can_parents_view.label',
                'help' => 'label.can_parents_view.help'
            ])
            ->add('sharedWith', SortableEntityType::class, [
                'label' => 'label.shared_with.label',
                'help' => 'label.shared_with.help',
                'class' => User::class,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->where('u.userType = :type')
                        ->setParameter('type', UserType::Teacher)
                        ->orderBy('u.username', 'ASC');
                }
            ]);
    }
}