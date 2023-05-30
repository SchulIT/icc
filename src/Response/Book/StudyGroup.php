<?php

namespace App\Response\Book;

use App\Response\UuidTrait;
use JMS\Serializer\Annotation as Serializer;

class StudyGroup {

    use UuidTrait;

    #[Serializer\SerializedName('name')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $name;

    #[Serializer\SerializedName('type')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnlyProperty]
    private readonly string $type;

    /**
     * @var Grade[]
     */
    #[Serializer\SerializedName('grades')]
    #[Serializer\Type('array<' .  Grade::class .'>')]
    #[Serializer\ReadOnlyProperty]
    private readonly array $grades;

    public function __construct(string $uuid, string $name, string $type, array $grades) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->type = $type;
        $this->grades = $grades;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getGrades(): array {
        return $this->grades;
    }
}