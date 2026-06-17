<?php

namespace App\Book\Form;

use App\Common\Form\Choice\StudentsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ReportRemarkType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('student', StudentsType::class, [
                'label' => 'label.student',
                'multiple' => false,
            ])
            ->add('remark', TextareaType::class, [
                'label' => 'label.comment',
            ]);
    }
}
