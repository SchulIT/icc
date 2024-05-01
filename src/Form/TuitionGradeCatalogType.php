<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TuitionGradeCatalogType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('displayName', TextType::class, [
                'label' => 'label.display_name'
            ])
            ->add('grades', CollectionType::class, [
                'label' => 'label.grade_values.label',
                'help' => 'label.grade_values.help',
                'entry_type' => TuitionGradeCategoryGradeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ]);
    }
}