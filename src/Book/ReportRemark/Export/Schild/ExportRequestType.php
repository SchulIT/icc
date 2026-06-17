<?php

namespace App\Book\ReportRemark\Export\Schild;

use App\Common\Entity\Section;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ExportRequestType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('section', EntityType::class, [
                'label' => 'label.section',
                'class' => Section::class
            ])
            ->add('filename', TextType::class, [
                'label' => 'book.export.schild_zeugnisbemerkungen.filename.label',
            ]);
    }
}
