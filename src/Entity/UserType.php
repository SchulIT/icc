<?php

namespace App\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum UserType: string implements TranslatableInterface {
    case Teacher = 'teacher';
    case Student = 'student';
    case Parent = 'parent';
    case Staff = 'staff';
    case Intern = 'intern';
    case User = 'user';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return $translator->trans(sprintf('user_type.%s', $this->value), domain: 'enums', locale: $locale);
    }
}