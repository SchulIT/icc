<?php

namespace App\Untis;

class GpuSupervision {

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
     * @return GpuSupervision
     */
    public function setCorridor(string $corridor): GpuSupervision {
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
     * @return GpuSupervision
     */
    public function setTeacher(string $teacher): GpuSupervision {
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
     * @return GpuSupervision
     */
    public function setDay(int $day): GpuSupervision {
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
     * @return GpuSupervision
     */
    public function setLesson(int $lesson): GpuSupervision {
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
     * @return GpuSupervision
     */
    public function setWeeks(array $weeks): GpuSupervision {
        $this->weeks = $weeks;
        return $this;
    }
}