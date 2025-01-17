<?php

namespace App\Form;

use App\Sorting\SortingStrategyInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortableCollectionType extends CollectionType {
    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefault('sort_by', null);
    }


    public function finishView(FormView $view, FormInterface $form, array $options): void {
        parent::finishView($view, $form, $options);

        $sortingStrategy = $options['sort_by'];
        if($sortingStrategy instanceof SortingStrategyInterface) {
            uasort($view->children, fn(FormView $a, FormView $b) => $sortingStrategy->compare($a->vars['value'], $b->vars['value']));
        }
    }

}