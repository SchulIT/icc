<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReturnItemTypeType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('displayName', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('note', MarkdownType::class, [
                'label' => 'admin.return_item_types.note.label',
                'help' => 'admin.return_item_types.note.help',
                'required' => false
            ])
            ->add('notificationNote', TextareaType::class, [
                'label' => 'admin.return_item_types.notification_note.label',
                'help' => 'admin.return_item_types.notification_note.help',
                'required' => true
            ]);
    }
}