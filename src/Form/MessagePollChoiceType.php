<?php

namespace App\Form;

use App\Entity\MessagePollChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessagePollChoiceType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('label', TextType::class, [
                'label' => 'label.messages_poll.choice.label'
            ])
            ->add('description', MarkdownType::class, [
                'label' => 'label.description',
                'required' => false
            ])
            ->add('minimum', IntegerType::class, [
                'label' => 'label.messages_poll.choice.minimum.label',
                'help' => 'label.messages_poll.choice.minimum.help',
                'data' => 0
            ])
            ->add('maximum', IntegerType::class, [
                'label' => 'label.messages_poll.choice.maximum.label',
                'help' => 'label.messages_poll.choice.maximum.help',
                'data' => 0
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', MessagePollChoice::class);
    }

    public function getBlockPrefix(): string {
        return 'message_poll_choice';
    }
}