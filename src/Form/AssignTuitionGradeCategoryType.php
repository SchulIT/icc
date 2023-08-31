<?php

namespace App\Form;

use App\Entity\TuitionGradeCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AssignTuitionGradeCategoryType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('categories', EntityType::class, [
                'label' => 'label.category',
                'class' => TuitionGradeCategory::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('c')
                        ->addOrderBy('c.position', 'asc');
                },
                'attr' => [
                    'data-choice' => 'true'
                ],
                'placeholder' => 'label.select.category',
                'multiple' => true,
                'choice_label' => fn(TuitionGradeCategory $category) => $category->getDisplayName() . (!empty($category->getComment()) ? ' [' . $category->getComment() . ']' : '')
            ])
            ->add('tuitions', TuitionChoiceType::class, [
                'label' => 'label.tuitions',
                'multiple' => true,
                'attr' => [
                    'size' => 10
                ]
            ])
        ;
    }
}