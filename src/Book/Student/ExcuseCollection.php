<?php

namespace App\Book\Student;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class ExcuseCollection extends ArrayCollection {

    public function __construct(private DateTime $date, private int $lesson, array $excuseNotes = [ ]) {
        parent::__construct($excuseNotes);
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getLesson(): int {
        return $this->lesson;
    }
}