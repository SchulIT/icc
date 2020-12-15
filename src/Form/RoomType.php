<?php

namespace App\Form;

use App\Repository\RoomTagRepositoryInterface;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                        ])
                        ->add('isReservationEnabled', CheckboxType::class, [
                            'label' => 'label.reservation_enabled.label',
                            'help' => 'label.reservation_enabled.help',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
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