<?php

namespace App\Book\Student;

use App\Entity\AttendanceFlag;
use JsonSerializable;

class AttendanceFlagCount implements JsonSerializable {
    public function __construct(private readonly AttendanceFlag $flag, private readonly int $count) {

    }

    /**
     * @return int
     */
    public function getCount(): int {
        return $this->count;
    }

    /**
     * @return AttendanceFlag
     */
    public function getFlag(): AttendanceFlag {
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