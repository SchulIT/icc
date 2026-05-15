<?php

namespace App\Common\Grouping;

use App\Common\Entity\Teacher;
use App\Framework\Grouping\GroupInterface;

/**
 * @implements GroupInterface<string, Teacher>
 */
class TeacherFirstCharacterGroup implements GroupInterface {

    /** @var Teacher[]  */
    private array $teachers = [ ];

    public function __construct(private readonly string $firstCharacter)
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

    public function getKey(): string {
        return $this->firstCharacter;
    }

    public function addItem($item): void {
        $this->teachers[] = $item;
    }
}