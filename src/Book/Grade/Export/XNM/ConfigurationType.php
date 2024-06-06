<?php

namespace App\Book\Grade\Export\XNM;

use App\Entity\Section;
use App\Entity\Tuition;
use App\Entity\TuitionGradeCategory;
use App\Entity\User;
use App\Form\GradeChoiceType;
use App\Form\TuitionChoiceType;
use App\Section\SectionResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ConfigurationType extends AbstractType {

    public function __construct(private readonly SectionResolverInterface $sectionResolver, private readonly TokenStorageInterface $tokenStorage) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'label' => 'label.section',
                'choice_label' => fn(Section $section) => $section->getDisplayName(),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('tuitions', TuitionChoiceType::class, [
                'label' => 'label.tuitions',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function(EntityRepository $repository) {
                    $section = $this->sectionResolver->getCurrentSection();

                    $qb = $repository
                        ->createQueryBuilder('t')
                        ->select(['t', 's', 'sg', 'g']);

                    if($section !== null) {
                        $qb->leftJoin('t.section', 's')
                            ->leftJoin('t.studyGroup', 'sg')
                            ->leftJoin('sg.grades', 'g')
                            ->where('s.id = :section')
                            ->setParameter('section', $section->getId());
                    }

                    $user = $this->tokenStorage->getToken()?->getUser();

                    if($user instanceof User && $user->getTeacher() !== null) {
                        $qb->leftJoin('t.teachers', 'teachers')
                            ->andWhere('teachers.id = :teacher')
                            ->setParameter('teacher', $user->getTeacher()->getId());
                    }

                    return $qb;
                }
            ])
            ->add('notenKategorie', EntityType::class, [
                'class' => TuitionGradeCategory::class,
                'label' => 'Notenkategorie',
                'choice_label' => fn(TuitionGradeCategory $category) => $category->getDisplayName() . (!empty($category->getComment()) ? ' (' . $category->getComment() . ')' : ''),
                'attr' => [
                    'data-choice' => 'true'
                ]
            ]);
    }
}