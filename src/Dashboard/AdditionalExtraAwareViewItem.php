<?php

namespace App\Dashboard;

use App\Entity\BookStudentInformation;
use App\Grouping\AbsentStudentGroup;

abstract class AdditionalExtraAwareViewItem extends AbstractViewItem {

    /**
     * @param AbsentStudentGroup[] $absentStudentGroups
     * @param BookStudentInformation[] $studentInfo
     */
    public function __construct(private readonly array $absentStudentGroups, private readonly array $studentInfo)
    {
    }

    /**
     * @return AbsentStudentGroup[]
     */
    public function getAbsentStudentGroups(): array {
        return $this->absentStudentGroups;
    }

    /**
     * @return BookStudentInformation[]
     */
    public function getStudentInfo(): array {
        return $this->studentInfo;
    }

    public function getAbsentStudentsCount(): int {
        $count = 0;
        $studentIds = [ ];

        foreach($this->absentStudentGroups as $group) {
            foreach($group->getStudents() as $student) {
                if(!in_array($student->getStudent()->getId(), $studentIds)) {
                    $count++;
                    $studentIds[] = $student->getStudent()->getId();
                }
            }
        }

        return $count;
    }
}