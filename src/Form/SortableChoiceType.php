<?php

namespace App\Form;

use App\Sorting\SortingStrategyInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortableChoiceType extends ChoiceType {

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver->setDefault('sort_by', null);
        $resolver->setDefault('sort_items_by', null);
    }

    public function finishView(FormView $view, FormInterface $form, array $options) {
        parent::finishView($view, $form, $options);

        $sortingStrategy = $options['sort_by'];
        if($sortingStrategy !== null && $sortingStrategy instanceof SortingStrategyInterface) {
            if($options['group_by'] !== null) {
                // In case the items are grouped, we must sort by the group label...
                uasort($view->vars['choices'], function (ChoiceGroupView $a, ChoiceGroupView $b) use ($sortingStrategy) {
                    return $sortingStrategy->compare($a->label, $b->label);
                });
            } else {
                // ... otherwise group by underlying data
                uasort($view->vars['choices'], function (ChoiceView $a, ChoiceView $b) use ($sortingStrategy) {
                    return $sortingStrategy->compare($a->data, $b->data);
                });
            }
        }

        $itemsSortingStrategy = $options['sort_items_by'];
        if($itemsSortingStrategy !== null && $itemsSortingStrategy instanceof SortingStrategyInterface) {
            foreach($view->vars['choices'] as $choiceGroupView) {
                uasort($choiceGroupView->choices, function(ChoiceView $a, ChoiceView $b) use ($itemsSortingStrategy) {
                    return $itemsSortingStrategy->compare($a->data, $b->data);
                });
            }
        }
    }
}