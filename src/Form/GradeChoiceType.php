<?php

namespace App\Form;

use App\Entity\Grade;
use App\Sorting\GradeNameStrategy;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeChoiceType extends SortableEntityType {

    public function __construct(private readonly GradeNameStrategy $gradeStrategy, ManagerRegistry $registry) {
        parent::__construct($registry);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('class', Grade::class)
            ->setDefault('sort_by', $this->gradeStrategy);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void {
        parent::buildView($view, $form, $options);

        $view->vars['attr']['data-choice'] = 'true';

        $gradeIds = [ ];
        foreach($view->vars['choices'] as $choice) {
            $gradeIds[] = $choice->value;
        }

        $view->vars['grades'] = $gradeIds;
    }

    public function getBlockPrefix(): string {
        return 'grade_choice';
    }
}