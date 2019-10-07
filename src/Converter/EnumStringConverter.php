<?php

namespace App\Converter;

use MyCLabs\Enum\Enum;
use Symfony\Contracts\Translation\TranslatorInterface;

class EnumStringConverter {

    private $translator;
    private $enumFormKeyMapping;

    public function __construct(TranslatorInterface $translator, array $enumFormKeyMapping) {
        $this->translator = $translator;
        $this->enumFormKeyMapping = $enumFormKeyMapping;
    }

    public function convert(Enum $enum): string {
        $prefix = $this->enumFormKeyMapping[get_class($enum)];
        $key = sprintf('%s.%s', $prefix, $enum->getValue());

        return $this->translator->trans($key, [], 'enums');
    }
}