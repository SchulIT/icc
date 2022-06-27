<?php

namespace App\Untis\Gpu\Supervision;

class Supervision {

    /**
     * @var string
     */
    private $corridor;

    /**
     * @var string
     */
    private $teacher;

    /**
     * @var int
     */
    private $day;

    /**
     * @var int
     */
    private $lesson;

    /**
     * @var int[]
     */
    private $weeks = [ ];

    /**
     * @return string
     */
    public function getCorridor(): string {
        return $this->corridor;
    }

    /**
     * @param string $corridor
     * @return Supervision
     */
    public function setCorridor(string $corridor): Supervision {
        $this->corridor = $corridor;
        return $this;
    }

    /**
     * @return string
     */
    public function getTeacher(): string {
        return $this->teacher;
    }

    /**
     * @param string $teacher
     * @return Supervision
     */
    public function setTeacher(string $teacher): Supervision {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return int
     */
    public function getDay(): int {
        return $this->day;
    }

    /**
     * @param int $day
     * @return Supervision
     */
    public function setDay(int $day): Supervision {
        $this->day = $day;
        return $this;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }

    /**
     * @param int $lesson
     * @return Supervision
     */
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
     * @return Supervision
     */
    public function setWeeks(array $weeks): Supervision {
        $this->weeks = $weeks;
        return $this;
    }
}