<?php

namespace App\Form;

use App\Repository\RoomTagRepositoryInterface;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomType extends AbstractType {



    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('externalId', TextType::class, [
                            'label' => 'label.external_id',
                            'required' => false
                        ])
                        ->add('name', TextType::class, [
                            'label' => 'label.name'
                        ])
                        ->add('description', TextType::class, [
                            'label' => 'label.description',
                            'required' => false
                        ])
                        ->add('capacity', IntegerType::class, [
                            'label' => 'label.capacity',
                            'required' => false
                        ]);
                }
            ])
            ->add('group_tags', FieldsetType::class, [
                'legend' => 'label.tags',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('tags', RoomTagChoiceType::class, [
                            'label' => '',
                            'required' => false
                        ]);
                }
            ]);
    }
}