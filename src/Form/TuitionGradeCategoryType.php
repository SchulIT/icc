<?php

namespace App\Form;

use App\Entity\TuitionGradeCatalog;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TuitionGradeCategoryType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('displayName', TextType::class, [
                'label' => 'label.display_name'
            ])
            ->add('comment', TextType::class, [
                'label' => 'label.comment',
                'required' => false
            ])
            ->add('position', IntegerType::class, [
                'label' => 'label.position.label',
                'help' => 'label.position.help'
            ])
            ->add('gradeType', EntityType::class, [
                'label' => 'label.tuition_grade_type',
                'class' => TuitionGradeCatalog::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('t')
                        ->addOrderBy('t.displayName', 'asc');
                },
                'choice_label' => fn(TuitionGradeCatalog $type) => $type->getDisplayName(),
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ]
            ])
            ->add('isExportable', CheckboxType::class, [
                'label' => 'label.exportable.label',
                'help' => 'label.exportable.help',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ],
                'required' => false
            ])
            ->add('tuitions', TuitionChoiceType::class, [
                'attr' => [
                    'size' => 10
                ],
                'label' => 'label.tuitions',
                'multiple' => true,
                'required' => false
            ]);
    }
}