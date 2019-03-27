<?php

namespace App\Grouping;

use App\Entity\Teacher;

class TeacherFirstCharacterGroup implements GroupInterface {

    /** @var string  */
    private $firstCharacter;

    /** @var Teacher[]  */
    private $teachers = [ ];

    public function __construct(string $firstCharacter) {
        $this->firstCharacter = $firstCharacter;
    }

    /**
     * @return string
     */
    public function getFirstCharacter(): string {
        return $this->firstCharacter;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers() {
        return $this->teachers;
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->firstCharacter;
    }

    /**
     * @param Teacher $item
     */
    public function addItem($item) {
        $this->teachers[] = $item;
    }
}