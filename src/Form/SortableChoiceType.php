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
                uasort($view->vars['choices'], fn(ChoiceGroupView $a, ChoiceGroupView $b) => $sortingStrategy->compare($a->label, $b->label));
            } else {
                // ... otherwise group by underlying data
                uasort($view->vars['choices'], fn(ChoiceView $a, ChoiceView $b) => $sortingStrategy->compare($a->data, $b->data));
            }
        }

        $itemsSortingStrategy = $options['sort_items_by'];
        if($itemsSortingStrategy !== null && $itemsSortingStrategy instanceof SortingStrategyInterface) {
            foreach($view->vars['choices'] as $choiceGroupView) {
                uasort($choiceGroupView->choices, fn(ChoiceView $a, ChoiceView $b) => $itemsSortingStrategy->compare($a->data, $b->data));
            }
        }
    }
}