<?php

namespace App\Common\Converter;

use App\Common\Converter\GradesCollapsedArrayConverter;
use App\Common\Entity\Grade;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupType;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudyGroupStrategy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudyGroupsGradeStringConverter {

    public function __construct(private TranslatorInterface $translator, private Sorter $sorter, private GradesCollapsedArrayConverter $gradeConverter)
    {
    }

    /**
     * @param StudyGroup[]|iterable $studyGroups
     * @param Grade[]|iterable $onlyGrades
     */
    public function convert(iterable $studyGroups, bool $sort = false, iterable $onlyGrades = [ ]): string {
        if($studyGroups instanceof Collection) {
            $studyGroups = $studyGroups->toArray();
        }

        if($onlyGrades instanceof Collection) {
            $onlyGrades = $onlyGrades->toArray();
        }

        /** @var int[] $onlyGrades */
        $onlyGrades = array_map(fn(Grade $grade) => $grade->getId(), $onlyGrades);

        if($sort === true) {
            $this->sorter->sort($studyGroups, StudyGroupStrategy::class);
        }

        $output = [ ];

        // First: Grades
        $grades = array_filter($studyGroups, fn(StudyGroup $group) => $group->getType() === StudyGroupType::Grade);

        $output = $this->gradeConverter->convert(
            array_map(fn(StudyGroup $group) => $group->getGrades()->first(), $grades)
        );

        // Second: Individual groups
        $studyGroups = array_filter($studyGroups, fn(StudyGroup $group) => $group->getType() !== StudyGroupType::Grade);

        $output += array_map(fn(StudyGroup $studyGroup) => $this->translator->trans('studygroup.string', [
            '%name%' => $studyGroup->getName(),
            '%grade%' => implode(', ', $this->gradeConverter->convert($studyGroup->getGrades()->filter(function(Grade $grade) use($onlyGrades) {
                if(empty($onlyGrades)) {
                    return true;
                }

                return in_array($grade->getId(), $onlyGrades);
            })->toArray()))
        ]), $studyGroups);

        return implode(', ', $output);
    }
}