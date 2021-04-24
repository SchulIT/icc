<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\UrlType;

class RedirectUriType extends UrlType {
    public function getBlockPrefix() {
        return 'redirect_uri';
    }
}