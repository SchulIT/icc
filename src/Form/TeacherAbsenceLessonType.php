<?php

namespace App\Form;

use App\Converter\TimetableLessonStringConverter;
use App\Entity\TeacherAbsenceLesson;
use App\Entity\TimetableLesson;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeacherAbsenceLessonType extends AbstractType {

    public function __construct(private readonly TimetableLessonStringConverter $converter) { }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('lesson', EntityType::class, [
                'class' => TimetableLesson::class,
                'label' => 'label.timetable_lesson',
                'choice_label' => fn(TimetableLesson $lesson) => $this->converter->convert($lesson),
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('l');
                }
            ])
            ->add('commentTeacher', MarkdownType::class, [
                'label' => 'absences.teachers.comment.teacher'
            ])
            ->add('commentStudents', MarkdownType::class, [
                'label' => 'absences.teachers.comment.students'
            ])
            ->add('comment', MarkdownType::class, [
                'label' => 'absences.teachers.comment.label'
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $lesson = $event->getData();
                $form = $event->getForm();

                if(!$lesson instanceof TeacherAbsenceLesson || $lesson->getAbsence() === null) {
                    return;
                }

                if($lesson->getLesson()?->getTuition() === null) {
                    $form->remove('commentTeacher')
                        ->remove('commentStudents');
                }

                $form->add('lesson', EntityType::class, [
                    'class' => TimetableLesson::class,
                    'label' => 'label.timetable_lesson',
                    'choice_label' => fn(TimetableLesson $lesson) => $this->converter->convert($lesson),
                    'query_builder' => function(EntityRepository $repository) use ($lesson) {
                        return $repository->createQueryBuilder('l')
                            ->leftJoin('l.tuition', 't')
                            ->leftJoin('t.teachers', 'teachers')
                            ->leftJoin('l.teachers', 'lTeachers')
                            ->andWhere('l.date >= :start')
                            ->andWhere('l.date <= :end')
                            ->andWhere(
                                $repository->createQueryBuilder('l')->expr()->orX(
                                    'teachers.id = :teacher',
                                    'lTeachers.id = :teacher'
                                )
                            )
                            ->setParameter('start', $lesson->getAbsence()->getFrom()->getDate())
                            ->setParameter('end', $lesson->getAbsence()->getUntil()->getDate())
                            ->setParameter('teacher', $lesson->getAbsence()->getTeacher())
                            ->orderBy('l.date', 'asc')
                            ->addOrderBy('l.lessonStart', 'asc');
                    }
                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('data_class', TeacherAbsenceLesson::class);
    }
}