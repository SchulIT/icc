<?php

namespace App\StudentAbsence\Form;

use App\Common\Form\Type\MarkdownType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StudentAbsenceMessageType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('message', MarkdownType::class, [
                'upload_enabled' => false
            ]);
    }
}