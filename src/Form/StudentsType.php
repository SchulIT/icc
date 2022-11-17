<?php

namespace App\Form;

use App\Converter\StudentStringConverter;
use App\Converter\StudyGroupStringConverter;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Repository\StudyGroupRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\Sorter;
use App\Sorting\StringStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\StudyGroupStrategy;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentsType extends SortableEntityType {

    public function __construct(ManagerRegistry $registry, private readonly StudyGroupRepositoryInterface $studyGroupRepository,
                                private readonly Sorter $sorter, private readonly StudyGroupStringConverter $studyGroupStringConverter,
                                private StudentStringConverter $studentConverter, private StudentStrategy $studentStrategy,
                                private StringStrategy $stringStrategy, private SectionResolverInterface $sectionResolver) {
        parent::__construct($registry);
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $section = $this->sectionResolver->getCurrentSection();

        $resolver
            ->setDefaults([
                'attr' => [
                    'size' => 15,
                    'data-choice' => 'true'
                ],
                'class' => Student::class,
                'multiple' => true,
                'choice_label' => fn(Student $student) => $this->studentConverter->convert($student, true, $section),
                'query_builder' => function(EntityRepository $repository) use($section) {
                    $qb = $repository
                        ->createQueryBuilder('s')
                        ->select(['s', 'm', 'g'])
                        ->leftJoin('s.gradeMemberships', 'm')
                        ->leftJoin('m.grade', 'g');

                    if($section !== null) {
                        $qb->leftJoin('s.sections', 'sec')
                            ->where('sec.id = :section')
                            ->setParameter('section', $section->getId());
                    }

                    return $qb;
                },
                'group_by' => function(Student $student) use($section) {
                    $grade = $student->getGrade($section);

                    if($grade !== null) {
                        return $grade->getName();
                    }

                    return '';
                },
                'sort_by' => $this->stringStrategy,
                'sort_items_by' => $this->studentStrategy,
                'apply_from_studygroups' => false
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        parent::buildView($view, $form, $options);

        $view->vars['apply_from_studygroups'] = $options['apply_from_studygroups'];

        if($options['apply_from_studygroups'] !== true) {
            return;
        }

        $studyGroups = $this->studyGroupRepository->findAllBySection($this->sectionResolver->getCurrentSection());
        $this->sorter->sort($studyGroups, StudyGroupStrategy::class);
        $choices = [ ];

        foreach($studyGroups as $studyGroup) {
            if($studyGroup->getMemberships()->count() === 0) {
                continue;
            }

            $choices[] = [
                'label' => $this->studyGroupStringConverter->convert($studyGroup, false, true),
                'students' => array_map(fn(StudyGroupMembership $membership) => $membership->getStudent()->getId(), $studyGroup->getMemberships()->toArray())
            ];
        }

        $view->vars['studygroups'] = $choices;
    }

    public function getBlockPrefix(): string {
        return 'students';
    }
}