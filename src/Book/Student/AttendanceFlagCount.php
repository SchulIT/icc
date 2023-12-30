<?php

namespace App\Book\Student;

use App\Entity\LessonAttendanceFlag;
use JsonSerializable;

class AttendanceFlagCount implements JsonSerializable {
    public function __construct(private readonly LessonAttendanceFlag $flag, private readonly int $count) {

    }

    /**
     * @return int
     */
    public function getCount(): int {
        return $this->count;
    }

    /**
     * @return LessonAttendanceFlag
     */
    public function getFlag(): LessonAttendanceFlag {
        return $this->flag;
    }

    public function jsonSerialize(): array {
        return [
            'count' => $this->count,
            'flag' => [
                'uuid' => $this->flag->getUuid()->toString(),
                'description' => $this->flag->getDescription()
            ]
        ];
    }
}