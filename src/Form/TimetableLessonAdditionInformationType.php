<?php

namespace App\Form;

use App\Entity\Tuition;
use App\Entity\User;
use App\Repository\StudyGroupRepository;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TimetableLessonAdditionInformationType extends AbstractType {

    public function __construct(private readonly SectionResolverInterface $sectionResolver,
                                private readonly TuitionRepositoryInterface $tuitionRepository,
                                private readonly TokenStorageInterface $tokenStorage) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'label.date'
            ])
            ->add('lessonStart', IntegerType::class, [
                'label' => 'label.start'
            ])
            ->add('lessonEnd', IntegerType::class, [
                'label' => 'label.end'
            ])
            ->add('study_group', StudyGroupType::class, [
                'label' => 'label.study_group',
                'query_builder' => function (EntityRepository $repository) {
                    $section = $this->sectionResolver->getCurrentSection();

                    $user = $this->tokenStorage->getToken()->getUser();
                    assert($user instanceof User);
                    $teacher = $user->getTeacher();

                    $qb = $repository->createQueryBuilder('sg')
                        ->select(['sg', 'g'])
                        ->orderBy('sg.name', 'asc')
                        ->leftJoin('sg.grades', 'g');

                    if($section !== null) {
                        $qb->leftJoin('sg.section', 's')
                            ->where('s.id = :section')
                            ->setParameter('section', $section->getId());
                    }

                    if($teacher !== null && $section !== null) {
                        $tuitions = $this->tuitionRepository->findAllByTeacher($teacher, $section);
                        $studyGroupIds = array_map(fn(Tuition $tuition) => $tuition->getStudyGroup()->getId(), $tuitions);

                        $qb->andWhere($qb->expr()->in('sg.id', ':studyGroups'))
                            ->setParameter('studyGroups', $studyGroupIds);
                    }

                    return $qb;
                }
            ])
            ->add('commentTeacher', MarkdownType::class, [
                'label' => 'absences.teachers.comment.teacher'
            ])
            ->add('commentStudents', MarkdownType::class, [
                'label' => 'absences.teachers.comment.students'
            ]);
    }
}