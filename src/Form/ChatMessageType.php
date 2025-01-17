<?php

namespace App\Form;

use App\Entity\ChatMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChatMessageType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('content', MarkdownType::class, [
                'label' => 'label.content'
            ])
            ->add('attachments', CollectionType::class, [
                'entry_type' => ChatMessageAttachmentType::class,
                'allow_add' => true,
                'allow_delete' => false,
                'by_reference' => false
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
                $data = $event->getData();

                if(isset($data['attachments'])) {
                    $data['attachments'] = array_values($data['attachments']);
                    $event->setData($data);
                }
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $data = $event->getData();

                if($data->getId() !== null) {
                    $event->getForm()->remove('attachments');
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefault('data_class', ChatMessage::class);
    }

}