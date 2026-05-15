<?php

namespace App\Exam\Form;

use App\Common\Converter\StudentStringConverter;
use App\Exam\Entity\Exam;
use App\Common\Entity\Student;
use App\Common\Entity\Tuition;
use App\Exam\Form\ExamStudentType;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Framework\Sorting\StringStrategy;
use App\Common\Sorting\StudentStrategy;
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