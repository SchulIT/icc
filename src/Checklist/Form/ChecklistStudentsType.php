<?php

namespace App\Checklist\Form;

use App\Checklist\Form\ChecklistStudentType;
use App\Common\Form\Type\SortableCollectionType;
use App\Checklist\Sorting\ChecklistStudentStudentStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ChecklistStudentsType extends AbstractType {

    public function __construct(private readonly ChecklistStudentStudentStrategy $checkboxStudentStrategy) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('students', SortableCollectionType::class, [
                'label' => '',
                'entry_type' => ChecklistStudentType::class,
                'allow_add' => false,
                'allow_delete' => true,
                'by_reference' => false,
                'sort_by' => $this->checkboxStudentStrategy
            ]);
    }
}