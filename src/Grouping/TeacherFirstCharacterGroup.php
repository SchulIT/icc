<?php

namespace App\Grouping;

use App\Entity\Teacher;

class TeacherFirstCharacterGroup implements GroupInterface {

    /** @var Teacher[]  */
    private array $teachers = [ ];

    public function __construct(private string $firstCharacter)
    {
    }

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