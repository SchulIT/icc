<?php

namespace App\Dashboard;

use App\Grouping\AbsentStudentGroup;

abstract class AbsenceAwareViewItem extends AbstractViewItem {

    /** @var AbsentStudentGroup[] */
    private $absentStudentGroups = [ ];

    public function __construct(array $absentStudentGroups) {
        $this->absentStudentGroups = $absentStudentGroups;
    }

    /**
     * @return AbsentStudentGroup[]
     */
    public function getAbsentStudentGroups(): array {
        return $this->absentStudentGroups;
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