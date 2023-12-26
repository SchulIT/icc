<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\Tuition;
use App\Repository\StudentRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\StringStrategy;
use App\Sorting\StudentStrategy;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ExamStudentsType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('students', CollectionType::class, [
                'entry_type' => ExamStudentType::class,
                'entry_options' => [  ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ]);
    }
}