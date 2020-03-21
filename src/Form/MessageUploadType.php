<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\MessageFile;
use App\Validator\FileExtension;
use Mimey\MimeTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MessageUploadType extends AbstractType {

    private $mimeTypes;

    public function __construct(MimeTypes $mimeTypes) {
        $this->mimeTypes = $mimeTypes;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefined('message');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        /** @var Message $message */
        $message = $options['message'];
        /** @var MessageFile[] $files */
        $files = $message->getFiles();

        foreach($files as $file) {
            $id = sprintf('file_%d', $file->getId());

            $builder
                ->add($id, FileType::class, [
                    'label' => $file->getLabel(),
                    'constraints' => [
                        new File([
                            'maxSize' => '10M'
                        ]),
                        new FileExtension([
                            'extensions' => explode(',', $file->getExtension())
                        ])
                    ],
                    'required' => false
                ]);
        }
    }
}