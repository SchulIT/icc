<?php

namespace App\Form;

use App\Entity\Grade;
use App\Entity\Tuition;
use App\Section\SectionResolverInterface;
use App\Sorting\StringStrategy;
use App\Sorting\TuitionStrategy;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ExamBulkType extends AbstractType {

    public function __construct(private TuitionStrategy $tuitionStrategy, private StringStrategy $stringStrategy, private readonly SectionResolverInterface $sectionResolver)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('number', IntegerType::class, [
                'label' => 'admin.exams.bulk.number_of_exams',
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0])
                ]
            ])
            ->add('tuitions', SortableEntityType::class, [
                'label' => 'label.tuitions',
                'attr' => [
                    'size' => 10
                ],
                'multiple' => true,
                'class' => Tuition::class,
                'query_builder' => function(EntityRepository $repository) {
                    if($this->sectionResolver->getCurrentSection() === null) {
                        return $repository->createQueryBuilder('t');
                    }

                    return $repository->createQueryBuilder('t')
                        ->where('t.section = :section')
                        ->setParameter('section', $this->sectionResolver->getCurrentSection());
                },
                'choice_label' => function(Tuition $tuition) {
                    if($tuition->getName() === $tuition->getStudyGroup()->getName()) {
                        return sprintf('%s - %s', $tuition->getName(), $tuition->getSubject()->getName());
                    }

                    return sprintf('%s - %s - %s', $tuition->getName(), $tuition->getStudyGroup()->getName(), $tuition->getSubject()->getName());
                },
                'group_by' => fn(Tuition $tuition) => implode(', ', $tuition->getStudyGroup()->getGrades()->map(fn(Grade $grade) => $grade->getName())->toArray()),
                'sort_by' => $this->stringStrategy,
                'sort_items_by' => $this->tuitionStrategy
            ])
            ->add('add_students', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.exams.students.add_all',
                'help' => 'admin.exams.students.info',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}