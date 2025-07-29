<?php

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Gender: string implements TranslatableInterface{
    case Male = 'male';
    case Female = 'female';
    case X = 'x';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return $translator->trans(sprintf('gender.%s', $this->value), domain: 'enums', locale: $locale);
    }
}