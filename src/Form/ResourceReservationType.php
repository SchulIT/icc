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

    public function __construct(private readonly ResourceStrategy $strategy)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('resource', SortableEntityType::class, [
                'label' => 'label.resource',
                'attr' => [
                    'size' => 10,
                    'data-choice' => 'true'
                ],
                'class' => ResourceEntity::class,
                'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('r')
                    ->where('r.isReservationEnabled = true'),
                'choice_label' => fn(ResourceEntity $resource) => sprintf('%s [%s]', $resource->getName(), $resource->getType()->getName()),
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
            ->add('teacher', TeacherChoiceType::class, [
                'label' => 'label.teacher'
            ])
            ->add('associatedStudyGroup', StudyGroupType::class, [
                'label' => 'label.associated_study_group.label',
                'help' => 'label.associated_study_group.help',
                'required' => false
            ]);
    }
}