<?php

namespace App\Form\Import\Untis;

use App\Form\SortableEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class SupervisionImportType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('start', DateType::class, [
                'label' => 'label.start',
                'placeholder' => 'label.choose',
                'help' => 'import.supervisions.help',
                'widget' => 'single_text'
            ])
            ->add('end', DateType::class, [
                'label' => 'label.end',
                'placeholder' => 'label.choose',
                'widget' => 'single_text'
            ])
            ->add('importFile', FileType::class, [
                'label' => 'GPU009.txt'
            ]);
    }
}