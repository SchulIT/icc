<?php

namespace App\Untis\Gpu\Supervision;

class Supervision {

    private ?string $corridor = null;

    private ?string $teacher = null;

    private ?int $day = null;

    private ?int $lesson = null;

    /**
     * @var int[]
     */
    private array $weeks = [ ];

    public function getCorridor(): string {
        return $this->corridor;
    }

    public function setCorridor(string $corridor): Supervision {
        $this->corridor = $corridor;
        return $this;
    }

    public function getTeacher(): string {
        return $this->teacher;
    }

    public function setTeacher(string $teacher): Supervision {
        $this->teacher = $teacher;
        return $this;
    }

    public function getDay(): int {
        return $this->day;
    }

    public function setDay(int $day): Supervision {
        $this->day = $day;
        return $this;
    }

    public function getLesson(): int {
        return $this->lesson;
    }

    public function setLesson(int $lesson): Supervision {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getWeeks(): array {
        return $this->weeks;
    }

    /**
     * @param int[] $weeks
     */
    public function setWeeks(array $weeks): Supervision {
        $this->weeks = $weeks;
        return $this;
    }
}