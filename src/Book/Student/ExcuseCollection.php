<?php

namespace App\Book\Student;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class ExcuseCollection extends ArrayCollection {

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $lesson;

    public function __construct(DateTime $date, int $lesson, array $excuseNotes = [ ]) {
        parent::__construct($excuseNotes);

        $this->date = $date;
        $this->lesson = $lesson;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getLesson(): int {
        return $this->lesson;
    }
}