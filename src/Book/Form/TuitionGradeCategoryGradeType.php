<?php

namespace App\Book\Form;

use App\Book\Entity\TuitionGradeCatalogGrade;
use App\Common\Form\Type\ColorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuitionGradeCategoryGradeType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('value', TextType::class, [
                'label' => 'label.value'
            ])
            ->add('color', ColorType::class, [
                'label' => 'label.color',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', TuitionGradeCatalogGrade::class);
    }
}