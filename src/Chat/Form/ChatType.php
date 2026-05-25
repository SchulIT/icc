<?php

namespace App\Chat\Form;

use App\Chat\Entity\Chat;
use App\Chat\Form\ChatMessageType;
use App\Chat\Form\ChatUserRecipientType;
use App\Chat\Form\Field\ChatUserAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChatType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('participants', ChatUserAutocompleteField::class, [
                'label' => 'label.recipients',
                'multiple' => true,
            ])
            ->add('topic', TextType::class, [
                'label' => 'label.topic',
                'required' => true
            ])
            ->add('messages', CollectionType::class, [
                'allow_add' => false,
                'allow_delete' => false,
                'entry_type' => ChatMessageType::class
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', Chat::class);
    }
}