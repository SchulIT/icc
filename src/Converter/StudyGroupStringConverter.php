<?php

namespace App\Converter;

use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudyGroupStringConverter {

    public function __construct(private TranslatorInterface $translator, private GradesStringConverter $gradeConverter)
    {
    }

    public function convert(StudyGroup $group, bool $short = false, bool $includeGrades = false): string {
        $name = $group->getName();

        $type = $this->translator->trans('studygroup.type.grade');

        if($group->getType() === StudyGroupType::Course) {
            $type = $this->translator->trans('studygroup.type.course');
        }

        if($short === false) {
            $name = $this->translator->trans('studygroup.name', [
                '%name%' => $name,
                '%type%' => $type
            ]);
        }

        if($includeGrades === true && $group->getType() !== StudyGroupType::Grade) {
            return sprintf('%s (%s)', $name, $this->gradeConverter->convert($group->getGrades()->toArray()));
        }

        return $name;
    }
}