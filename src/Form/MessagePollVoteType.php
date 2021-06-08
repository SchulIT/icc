<?php

namespace App\Form;

use App\Entity\MessagePollChoice;
use App\Utils\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessagePollVoteType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('num_choices');
        $resolver->setRequired('choices');
        $resolver->setRequired('allow_vote');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $choices = ArrayUtils::createArrayWithKeys($options['choices'], function(MessagePollChoice $choice) {
            return $choice->getLabel();
        });

        for($i = 1; $i <= $options['num_choices']; $i++) {
            $builder
                ->add(sprintf('%d', $i), ChoiceType::class, [
                    'label' => 'messages.poll.choice',
                    'label_translation_parameters' => [
                        '%rank%' => $i
                    ],
                    'choices' => $choices,
                    'placeholder' => 'choice.choose',
                    'property_path' => sprintf('[%d]', $i - 1),
                    'disabled' => $options['allow_vote'] !== true
                ]);
        }
    }
}