<?php

namespace App\Form;

use App\Entity\MessageFileUpload;
use App\Validator\FileExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MessageFileUploadType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('file', FileType::class, [
                'label' => '',
                'constraints' => [
                    new File([
                        'maxSize' => '10M'
                    ])
                ],
                'required' => false
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                /** @var MessageFileUpload $fileUpload */
                $fileUpload = $event->getData();
                $form = $event->getForm();

                if($fileUpload !== null) {
                    $form->add('file', FileType::class, [
                        'label' => $fileUpload->getMessageFile()->getLabel(),
                        'constraints' => [
                            new File([
                                'maxSize' => '10M'
                            ]),
                            new FileExtension([
                                'extensions' => explode(',', $fileUpload->getMessageFile()->getExtension())
                            ])
                        ],
                        'help' => 'messages.uploads.help',
                        'help_translation_parameters' => [
                            '%extensions%' => $fileUpload->getMessageFile()->getExtension()
                        ],
                        'required' => false
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', MessageFileUpload::class);
    }
}