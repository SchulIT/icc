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

    private $translator;
    private $sorter;

    public function __construct(TranslatorInterface $translator, Sorter $sorter) {
        $this->translator = $translator;
        $this->sorter = $sorter;
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

        return implode(', ', array_map(function(StudyGroup $studyGroup) {
            if($studyGroup->getType()->equals(StudyGroupType::Grade())) {
                return $studyGroup->getName();
            }

            return $this->translator->trans('studygroup.string', [
                '%name%' => $studyGroup->getName(),
                '%grade%' => implode(', ', $studyGroup->getGrades()->map(function(Grade $grade) {
                    return $grade->getName();
                })->toArray())
            ]);
        }, $studyGroups));
    }
}