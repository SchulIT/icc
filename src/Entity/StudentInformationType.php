<?php

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum StudentInformationType: string implements TranslatableInterface {
    case Lessons = 'lessons';
    case Exams = 'exams';
    case Health = 'health';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return $translator->trans(sprintf('student_information_type.%s', $this->value), domain: 'enums', locale: $locale);
    }
}