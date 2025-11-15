<?php

namespace App\ParentsDay;

use App\Entity\Room;
use App\Entity\Teacher;
use App\Entity\Tuition;

class TeacherItem {

    /**
     * @param string[] $comments
     * @param Tuition[] $tuitions
     */
    public function __construct(private readonly Teacher $teacher,
                                private readonly bool $isGradeTeacher,
                                private readonly bool $alreadyBooked,
                                private readonly bool $isAppointmentRequested,
                                private readonly bool $isAppointmentNotNecessary,
                                private readonly array $comments = [ ],
                                private readonly array $tuitions = [ ],
                                private readonly ?string $userUuid = null,
                                private readonly ?Room $room = null) {

    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    public function isGradeTeacher(): bool {
        return $this->isGradeTeacher;
    }

    public function isAlreadyBooked(): bool {
        return $this->alreadyBooked;
    }

    public function isAppointmentRequested(): bool {
        return $this->isAppointmentRequested;
    }

    public function isAppointmentNotNecessary(): bool {
        return $this->isAppointmentNotNecessary;
    }

    /**
     * @return string[]
     */
    public function getComments(): array {
        return $this->comments;
    }

    /**
     * @return Tuition[]
     */
    public function getTuitions(): array {
        return $this->tuitions;
    }

    public function getUserUuid(): ?string {
        return $this->userUuid;
    }

    public function getRoom(): ?Room {
        return $this->room;
    }
}