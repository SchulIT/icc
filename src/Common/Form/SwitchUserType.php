<?php

namespace App\Common\Form;

use App\Common\Form\Autocomplete\UserAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SwitchUserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('user', UserAutocompleteField::class, [
                'label' => 'label.user'
            ]);
    }
}