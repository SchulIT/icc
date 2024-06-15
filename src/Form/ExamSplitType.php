<?php

namespace App\Form;

use App\Entity\Exam;
use App\Entity\ExamStudent;
use App\Entity\Room;
use App\Entity\Student;
use App\Exam\ExamSplit;
use App\Sorting\RoomNameStrategy;
use App\Sorting\StudentStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamSplitType extends AbstractType {

    public function __construct(private readonly RoomNameStrategy $roomStrategy, private readonly StudentStrategy $studentStrategy, private readonly EntityManagerInterface $entityManager) {

    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefined('exam_id');
        $resolver->setRequired('exam_id');
        $resolver->setDefault('data_class', ExamSplit::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('firstStudent', SortableEntityType::class, [
                'multiple' => false,
                'label' => 'label.from',
                'class' => Student::class,
                'sort_by' => $this->studentStrategy,
                'query_builder' => function (EntityRepository $er) use($options) {
                    $subquery = $this->entityManager->createQueryBuilder()
                        ->select('sInner.id')
                        ->from(ExamStudent::class, 'esInner')
                        ->leftJoin('esInner.student', 'sInner')
                        ->where('esInner.exam = :exam_id');

                    return $er->createQueryBuilder('s')
                        ->where($subquery->expr()->in('s.id', $subquery->getDQL()))
                        ->setParameter('exam_id', $options['exam_id']);
                },
            ])
            ->add('lastStudent', SortableEntityType::class, [
                'multiple' => false,
                'label' => 'label.until',
                'class' => Student::class,
                'sort_by' => $this->studentStrategy,
                'query_builder' => function (EntityRepository $er) use($options) {
                    $subquery = $this->entityManager->createQueryBuilder()
                        ->select('sInner.id')
                        ->from(ExamStudent::class, 'esInner')
                        ->leftJoin('esInner.student', 'sInner')
                        ->where('esInner.exam = :exam_id');

                    return $er->createQueryBuilder('s')
                        ->where($subquery->expr()->in('s.id', $subquery->getDQL()))
                        ->setParameter('exam_id', $options['exam_id']);
                },
            ])
            ->add('room', SortableEntityType::class, [
                'label' => 'label.room',
                'attr' => [
                    'size' => 10,
                    'data-choice' => 'true'
                ],
                'class' => Room::class,
                'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('r')
                    ->where('r.isReservationEnabled = true'),
                'choice_label' => fn(Room $room) => $room->getName(),
                'sort_by' => $this->roomStrategy,
                'required' => false,
                'placeholder' => 'label.select.room',
                'help' => 'label.room_reservation_hint'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
                'required' => false
            ]);
    }
}