<?php

namespace App\Form;

use App\Converter\StudyGroupStringConverter;
use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Sorting\StringStrategy;
use App\Sorting\StudyGroupStrategy;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use App\Entity\StudyGroupType as StudyGroupEntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudyGroupType extends SortableEntityType {

    private $studyGroupConverter;
    private $stringStrategy;
    private $studyGroupStrategy;

    public function __construct(ManagerRegistry $registry, StudyGroupStringConverter $studyGroupConverter,
                                StringStrategy $stringStrategy, StudyGroupStrategy $studyGroupStrategy) {
        parent::__construct($registry);

        $this->stringStrategy = $stringStrategy;
        $this->studyGroupConverter = $studyGroupConverter;
        $this->studyGroupStrategy = $studyGroupStrategy;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('class', StudyGroup::class)
            ->setDefault('query_builder', function(EntityRepository $repository) {
                return $repository->createQueryBuilder('sg')
                    ->select(['sg', 'g'])
                    ->orderBy('sg.name', 'asc')
                    ->leftJoin('sg.grades', 'g');
            })
            ->setDefault('group_by', function(StudyGroup $group) {
                $grades = array_map(function(Grade $grade) {
                    return $grade->getName();
                }, $group->getGrades()->toArray());

                return join(', ', $grades);
            })
            ->setDefault('choice_label', function(StudyGroup $group) {
                return $this->studyGroupConverter->convert($group);
            })
            ->setDefault('sort_by', $this->stringStrategy)
            ->setDefault('sort_items_by', $this->studyGroupStrategy);

    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        $gradeIds = [ ]; // List of grade ids
        $choices = $view->vars['choices'];

        foreach($choices as $choice) {
            if($choice instanceof ChoiceGroupView) {
                foreach($choice->choices as $innerChoice) {
                    if($innerChoice->data instanceof StudyGroup && $innerChoice->data->getType()->equals(StudyGroupEntityType::Grade())) {
                        $gradeIds[] = $innerChoice->data->getId();
                    }
                }
            } else {
                if($choice->data instanceof StudyGroup && $choice->data->getType()->equals(StudyGroupEntityType::Grade())) {
                    $gradeIds[] = $choice->data->getId();
                }
            }
        }

        $view->vars['grades'] = $gradeIds;
    }

    public function getBlockPrefix()
    {
        return 'study_group';
    }
}