<?php

namespace App\Converter;

use App\Entity\Gender;
use Symfony\Contracts\Translation\TranslatorInterface;

class GenderStringConverter {
    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert(Gender $gender) {
        $key = sprintf('gender.%s', $gender->getValue());
        return $this->translator->trans($key);
    }
}