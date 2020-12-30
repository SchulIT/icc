<?php

namespace App\Form;

use App\Converter\TeacherStringConverter;
use App\Entity\ResourceEntity;
use App\Entity\Room;
use App\Entity\Teacher;
use App\Sorting\ResourceStrategy;
use App\Sorting\TeacherStrategy;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ResourceReservationType extends AbstractType {

    private $strategy;
    private $teacherStrategy;
    private $teacherConverter;

    public function __construct(ResourceStrategy $strategy, TeacherStrategy $teacherStrategy, TeacherStringConverter $teacherConverter) {
        $this->strategy = $strategy;
        $this->teacherStrategy = $teacherStrategy;
        $this->teacherConverter = $teacherConverter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('resource', SortableEntityType::class, [
                'label' => 'label.resource',
                'attr' => [
                    'size' => 10,
                    'data-choice' => 'true'
                ],
                'class' => ResourceEntity::class,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('r')
                        ->where('r.isReservationEnabled = true');
                },
                'choice_label' => function(ResourceEntity $resource) {
                    return sprintf('%s [%s]', $resource->getName(), $resource->getType()->getName());
                },
                'sort_by' => $this->strategy
            ])
            ->add('date', DateType::class, [
                'label' => 'label.date',
                'widget' => 'single_text'
            ])
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.start'
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.end'
            ])
            ->add('teacher', SortableEntityType::class, [
                'label' => 'label.teacher',
                'attr' => [
                    'size' => 10,
                    'data-choice' => 'true'
                ],
                'class' => Teacher::class,
                'choice_label' => function(Teacher $teacher) {
                    return $this->teacherConverter->convert($teacher);
                },
                'sort_by' => $this->teacherStrategy,

            ]);
    }
}