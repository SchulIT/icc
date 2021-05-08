<?php

namespace App\Converter;

use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudyGroupStringConverter {

    private $gradeConverter;
    private $translator;

    public function __construct(TranslatorInterface $translator, GradesStringConverter $gradeConverter) {
        $this->translator = $translator;
        $this->gradeConverter = $gradeConverter;
    }

    public function convert(StudyGroup $group, bool $short = false, bool $includeGrades = false): string {
        if($short === true) {
            return $group->getName();
        }

        $type = $this->translator->trans('studygroup.type.grade');

        if($group->getType()->equals(StudyGroupType::Course())) {
            $type = $this->translator->trans('studygroup.type.course');
        }

        $name = $this->translator->trans('studygroup.name', [
            '%name%' => $group->getName(),
            '%type%' => $type
        ]);

        if($includeGrades === true && $group->getType()->equals(StudyGroupType::Grade()) === false) {
            return sprintf('%s (%s)', $name, $this->gradeConverter->convert($group->getGrades()->toArray()));
        }

        return $name;
    }
}