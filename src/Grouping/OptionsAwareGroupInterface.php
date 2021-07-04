<?php

namespace App\Grouping;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface OptionsAwareGroupInterface {
    public function configureOptions(OptionsResolver $resolver);
}