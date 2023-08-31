<?php

namespace App\Form;

use SchulIT\CommonBundle\Form\FontAwesomeIconPicker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentCategoryType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('icon', FontAwesomeIconPicker::class, [
                'label' => 'label.icon.label',
                'help' => 'label.icon.help',
                'required' => false
            ]);
    }
}