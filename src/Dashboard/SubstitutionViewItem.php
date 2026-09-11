<?php

namespace App\Dashboard;

use App\Common\Entity\Room;
use App\Common\Entity\Student;
use App\Framework\Utils\ArrayUtils;
use App\Substitution\Entity\Substitution;
use App\Timetable\Entity\TimetableLesson;
use App\Timetable\Entity\TimetableLessonAdditionalInformation;

class SubstitutionViewItem extends AdditionalExtraAwareViewItem implements RoomStatusAware {

    use RoomStatusAwareTrait;

    /**
     * @param Substitution $substitution
     * @param bool $isFreeLessonType
     * @param array $students
     * @param array $absentStudentGroups
     * @param array $studentInfo
     * @param TimetableLesson|null $lesson
     * @param TimetableLessonAdditionalInformation[] $additionalInformation
     */
    public function __construct(private readonly Substitution $substitution, private readonly bool $isFreeLessonType, private readonly array $students, array $absentStudentGroups, array $studentInfo, private readonly ?TimetableLesson $lesson, private readonly array $additionalInformation, bool $hasAnyStudentWithHealthInfo) {
        parent::__construct($absentStudentGroups, $studentInfo, $hasAnyStudentWithHealthInfo);
    }

    public function isFreeLesson(): bool {
        return $this->isFreeLessonType;
    }

    public function getSubstitution(): Substitution {
        return $this->substitution;
    }

    /**
     * @return Room[]
     */
    public function getRooms(): array {
        $rooms = [ ];

        foreach($this->getSubstitution()->getRooms() as $room) {
            $rooms[] = $room;
        }

        foreach($this->getSubstitution()->getReplacementRooms() as $room) {
            $rooms[] = $room;
        }

        return ArrayUtils::unique($rooms);
    }

    public function getTimetableLesson(): ?TimetableLesson {
        return $this->lesson;
    }

    /**
     * @return TimetableLessonAdditionalInformation[]
     */
    public function getAdditionalInformation(): array {
        return $this->additionalInformation;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    public function getBlockName(): string {
        return 'substitution';
    }
}
