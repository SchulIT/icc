<?php

namespace App\Converter;

use App\Entity\Gender;
use App\Entity\Teacher;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeacherStringConverter {
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert(?Teacher $teacher, bool $includeAcronym = false): ?string {
        if($teacher === null) {
            return null;
        }

        $string = $this->translator->trans($includeAcronym ? 'teacher.fullname_acronym' : 'teacher.fullname', [
            '%title%' => $teacher->getTitle(),
            '%lastname%' => $teacher->getLastname(),
            '%firstname%' => $teacher->getFirstname(),
            '%acronym%' => $teacher->getAcronym()
        ]);

        $string = preg_replace('~\s+~', ' ', $string);
        $string = trim($string);

        return $string;
    }
}