<?php

namespace App\ReturnItem\Form;

use App\Common\Form\Choice\StudentsType;
use App\Common\Form\Type\MarkdownType;
use App\ReturnItem\Entity\ReturnItem;
use App\ReturnItem\Entity\ReturnItemType as ReturnItemTypeEntity;
use League\CommonMark\Extension\Highlight\Mark;
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
                'expanded' => true
            ])
            ->add('comment', MarkdownType::class, [
                'label' => 'label.comment',
                'required' => false
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