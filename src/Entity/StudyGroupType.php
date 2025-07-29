<?php

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum StudyGroupType: string implements TranslatableInterface {
    case Grade = 'grade';
    case Course = 'course';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return $translator->trans(sprintf('study_group_type.%s', $this->value), domain: 'enums', locale: $locale);
    }
}