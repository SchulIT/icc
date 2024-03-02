<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NewChatType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('recipients', ChatUserRecipientType::class, [
                'label' => 'label.recipients'
            ])
            ->add('topic', TextType::class, [
                'label' => 'label.topic',
                'required' => false
            ])
            ->add('message', MarkdownType::class, [
                'label' => 'label.content'
            ]);
    }
}