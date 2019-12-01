<?php

namespace App\Converter;

use App\Entity\Gender;
use App\Entity\Teacher;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeacherStringConverter {
    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert(?Teacher $teacher, bool $includeAcronym = false): ?string {
        if($teacher === null) {
            return null;
        }

        $greeting = $this->translator->trans('greeting.female');

        if($teacher->getGender()->equals(Gender::Male())) {
            $greeting = $this->translator->trans('greeting.male');
        } else if($teacher->getGender()->equals(Gender::X())) {
            // This should be fixed somehow!
            $greeting = '';
        }

        $string = $this->translator->trans($includeAcronym ? 'teacher.fullname_acronym' : 'teacher.fullname', [
            '%greeting%' => $greeting,
            '%title%' => $teacher->getTitle(),
            '%name%' => $teacher->getLastname(),
            '%acronym%' => $teacher->getAcronym()
        ]);

        $string = preg_replace('~\s+~', ' ', $string);
        $string = trim($string);

        return $string;
    }
}