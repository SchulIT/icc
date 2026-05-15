<?php

namespace App\Common\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class ColorType extends TextType {
    public function getBlockPrefix(): string {
        return 'color';
    }
}