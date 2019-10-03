<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class ColorType extends TextType {
    public function getBlockPrefix() {
        return 'color';
    }
}