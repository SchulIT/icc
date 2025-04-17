<?php

namespace App\Form;

use App\Entity\ReturnItem;
use App\Entity\ReturnItemType as ReturnItemTypeEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ReturnItemType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('student', StudentsType::class, [
                'multiple' => false,
                'label' => 'label.student'
            ])
            ->add('type', EntityType::class, [
                'label' => 'label.type',
                'class' => ReturnItemTypeEntity::class,
                'choice_label' => fn(ReturnItemTypeEntity $type) => $type->getDisplayName(),
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if($data instanceof ReturnItem && $data->getId() !== null) {
                    $form->add('student', StudentsType::class, [
                        'multiple' => false,
                        'label' => 'label.student',
                        'disabled' => true
                    ]);
                }
            });
    }
}