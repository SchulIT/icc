<?php

namespace App\Converter;

use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupType;
use App\Sorting\Sorter;
use App\Sorting\StudyGroupStrategy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudyGroupsGradeStringConverter {

    private $gradeConverter;
    private $translator;
    private $sorter;

    public function __construct(TranslatorInterface $translator, Sorter $sorter, GradesCollapsedArrayConverter $gradeConverter) {
        $this->translator = $translator;
        $this->sorter = $sorter;
        $this->gradeConverter = $gradeConverter;
    }

    /**
     * @param StudyGroup[]|ArrayCollection $studyGroups
     * @return string
     */
    public function convert(iterable $studyGroups, bool $sort = false) {
        if($studyGroups instanceof Collection) {
            $studyGroups = $studyGroups->toArray();
        }

        if($sort === true) {
            $this->sorter->sort($studyGroups, StudyGroupStrategy::class);
        }

        $output = [ ];

        // First: Grades
        $grades = array_filter($studyGroups, function(StudyGroup $group) {
            return $group->getType()->equals(StudyGroupType::Grade());
        });

        $output = $this->gradeConverter->convert(
            array_map(function(StudyGroup $group) {
                return $group->getGrades()->first();
            }, $grades)
        );

        // Second: Individual groups
        $studyGroups = array_filter($studyGroups, function (StudyGroup $group) {
            return $group->getType()->equals(StudyGroupType::Grade()) === false;
        });

        $output += array_map(function(StudyGroup $studyGroup) {
            return $this->translator->trans('studygroup.string', [
                '%name%' => $studyGroup->getName(),
                '%grade%' => implode(', ', $this->gradeConverter->convert($studyGroup->getGrades()->toArray()))
            ]);
        }, $studyGroups);

        return implode(', ', $output);
    }
}