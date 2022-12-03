<?php

namespace App\Converter;

use MyCLabs\Enum\Enum;
use Symfony\Contracts\Translation\TranslatorInterface;

class EnumStringConverter {

    public function __construct(private TranslatorInterface $translator, private array $enumFormKeyMapping)
    {
    }

    public function convert($enum): string {
        $prefix = $this->enumFormKeyMapping[$enum::class];

        if(enum_exists($enum::class)) {
            $key = sprintf('%s.%s', $prefix, $enum->value);
        } else {
            $key = sprintf('%s.%s', $prefix, $enum->getValue());
        }

        return $this->translator->trans($key, [], 'enums');
    }
}