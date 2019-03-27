<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\Message;

class MessageGradeGroup implements GroupInterface {

    /** @var Grade */
    private $grade;

    /** @var Message[] */
    private $messages = [ ];

    public function __construct(Grade $grade) {
        $this->grade = $grade;
    }

    /**
     * @return Grade
     */
    public function getGrade(): Grade {
        return $this->grade;
    }

    /**
     * @return Message[]
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * @return Grade
     */
    public function getKey() {
        return $this->grade;
    }

    /**
     * @param Message $item
     */
    public function addItem($item) {
        $this->messages[] = $item;
    }
}