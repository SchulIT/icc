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

    public function convert(Teacher $teacher) {
        $greeting = $this->translator->trans('greeting.female');

        if($teacher->getGender()->equals(Gender::Male())) {
            $greeting = $this->translator->trans('greeting.male');
        }

        return $this->translator->trans('teacher.fullname', [
            '%greeting%' => $greeting,
            '%title%' => $teacher->getTitle(),
            '%name%' => $teacher->getLastname()
        ]);
    }
}