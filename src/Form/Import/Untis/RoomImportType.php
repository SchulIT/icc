<?php

namespace App\Form\Import\Untis;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomImportType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('importFile', FileType::class, [
                'label' => 'GPU005.txt'
            ]);
    }
}