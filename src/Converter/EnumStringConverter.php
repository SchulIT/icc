<?php

namespace App\Converter;

use Symfony\Contracts\Translation\TranslatorInterface;

class EnumStringConverter {

    public function __construct(private TranslatorInterface $translator, private array $enumFormKeyMapping)
    {
        $this->enumFormKeyMapping = array_flip($this->enumFormKeyMapping);
    }

    public function convert($enum): string {
        $prefix = $this->enumFormKeyMapping[$enum::class];

        if(enum_exists($enum::class)) {
            $key = sprintf('%s.%s', $prefix, $enum->value);
        } else {
            $key = '';
        }

        return $this->translator->trans($key, [], 'enums');
    }
}