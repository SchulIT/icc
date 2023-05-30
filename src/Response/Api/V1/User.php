<?php

namespace App\Response\Api\V1;

use App\Entity\Section;
use App\Entity\UserType;
use App\Entity\User as UserEntity;
use App\Entity\Student as StudentEntity;
use JMS\Serializer\Annotation as Serializer;

class User {

    use UuidTrait;

    #[Serializer\SerializedName('username')]
    #[Serializer\Type('string')]
    private ?string $username = null;

    #[Serializer\SerializedName('firstname')]
    #[Serializer\Type('string')]
    private ?string $firstname = null;

    #[Serializer\SerializedName('lastname')]
    #[Serializer\Type('string')]
    private ?string $lastname = null;

    #[Serializer\SerializedName('email')]
    #[Serializer\Type('string')]
    private ?string $email = null;

    #[Serializer\SerializedName('teacher')]
    #[Serializer\Type(Teacher::class)]
    private ?Teacher $teacher = null;

    /**
     *
     * @var Student[]
     */
    #[Serializer\SerializedName('students')]
    #[Serializer\Type('array<App\Response\Api\V1\Student>')]
    private ?array $students = null;

    #[Serializer\SerializedName('type')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnly]
    #[Serializer\Accessor(getter: 'getTypeString')]
    private ?UserType $type = null;

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): User {
        $this->username = $username;
        return $this;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): User {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): User {
        $this->lastname = $lastname;
        return $this;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): User {
        $this->email = $email;
        return $this;
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): User {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @param Student[] $students
     */
    public function setStudents(array $students): User {
        $this->students = $students;
        return $this;
    }

    public function getType(): UserType {
        return $this->type;
    }

    public function setType(UserType $type): User {
        $this->type = $type;
        return $this;
    }

    public function getTypeString(): string {
        return $this->type->value;
    }

    public static function fromEntity(?UserEntity $userEntity, ?Section $section = null): ?self {
        if($userEntity === null) {
            return null;
        }

        return (new self())
            ->setUsername($userEntity->getUsername())
            ->setLastname($userEntity->getLastname())
            ->setFirstname($userEntity->getFirstname())
            ->setEmail($userEntity->getEmail())
            ->setUuid($userEntity->getUuid())
            ->setType($userEntity->getUserType())
            ->setTeacher(Teacher::fromEntity($userEntity->getTeacher()))
            ->setStudents(array_map(fn(StudentEntity $studentEntity) => Student::fromEntity($studentEntity, $section), $userEntity->getStudents()->toArray()));
    }
}