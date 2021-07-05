<?php

namespace App\Form;

use App\Converter\EnumStringConverter;
use App\Converter\StudyGroupStringConverter;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType as StudyGroupEntityType;
use App\Section\SectionResolver;
use App\Sorting\StringStrategy;
use App\Sorting\StudyGroupStrategy;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudyGroupType extends SortableEntityType {

    private $studyGroupConverter;
    private $stringStrategy;
    private $studyGroupStrategy;
    private $enumStringConverter;
    private $sectionResolver;

    public function __construct(ManagerRegistry $registry, StudyGroupStringConverter $studyGroupConverter,
                                StringStrategy $stringStrategy, StudyGroupStrategy $studyGroupStrategy,
                                EnumStringConverter $enumStringConverter, SectionResolver $sectionResolver) {
        parent::__construct($registry);

        $this->stringStrategy = $stringStrategy;
        $this->studyGroupConverter = $studyGroupConverter;
        $this->studyGroupStrategy = $studyGroupStrategy;
        $this->enumStringConverter = $enumStringConverter;
        $this->sectionResolver = $sectionResolver;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('class', StudyGroup::class)
            ->setDefault('query_builder', function(EntityRepository $repository) {
                $section = $this->sectionResolver->getCurrentSection();

                $qb = $repository->createQueryBuilder('sg')
                    ->select(['sg', 'g'])
                    ->orderBy('sg.name', 'asc')
                    ->leftJoin('sg.grades', 'g');

                if($section !== null) {
                    $qb->leftJoin('sg.section', 's')
                        ->where('s.id = :section')
                        ->setParameter('section', $section->getId());
                }

                return $qb;
            })
            ->setDefault('group_by', function(StudyGroup $group) {
                return $this->enumStringConverter->convert($group->getType());
            })
            ->setDefault('choice_label', function(StudyGroup $group) {
                return $this->studyGroupConverter->convert($group, false, true);
            })
            ->setDefault('sort_by', $this->stringStrategy)
            ->setDefault('sort_items_by', $this->studyGroupStrategy);

    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        $gradeIds = [ ]; // List of grade ids
        $choices = $view->vars['choices'];
        $view->vars['buttons'] = false;
        $view->vars['attr']['data-choice'] = 'true';
        $view->vars['placeholder'] = 'label.select.study_group';

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
        $view->vars['section'] = $this->sectionResolver->getCurrentSection();
    }

    public function getBlockPrefix()
    {
        return 'study_group';
    }
}