<?php

namespace App\Form;

use App\Entity\StudyGroup;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use App\Entity\StudyGroupType as StudyGroupEntityType;

class StudyGroupType extends SortableEntityType {

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