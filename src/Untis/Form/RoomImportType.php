<?php

namespace App\Untis\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomImportType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('importFile', FileType::class, [
                'label' => 'GPU005.txt'
            ]);
    }
}