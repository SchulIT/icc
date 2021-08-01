<?php

namespace App\Response\Api\V1;

use App\Entity\Section;
use App\Entity\UserType;
use App\Entity\User as UserEntity;
use App\Entity\Student as StudentEntity;
use JMS\Serializer\Annotation as Serializer;

class User {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("username")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $username;

    /**
     * @Serializer\SerializedName("firstname")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $firstname;

    /**
     * @Serializer\SerializedName("lastname")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $lastname;

    /**
     * @Serializer\SerializedName("email")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $email;

    /**
     * @Serializer\SerializedName("teacher")
     * @Serializer\Type("App\Response\Api\V1\Teacher")
     *
     * @var Teacher|null
     */
    private $teacher;

    /**
     * @Serializer\SerializedName("students")
     * @Serializer\Type("array<App\Response\Api\V1\Student>")
     *
     * @var Student[]
     */
    private $students;

    /**
     * @Serializer\SerializedName("type")
     * @Serializer\Type("string")
     * @Serializer\ReadOnly()
     * @Serializer\Accessor(getter="getTypeString")
     *
     * @var UserType
     */
    private $type;

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): string {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return User
     */
    public function setFirstname(string $firstname): User {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): string {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return User
     */
    public function setLastname(string $lastname): User {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User {
        $this->email = $email;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return User
     */
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
     * @return User
     */
    public function setStudents(array $students): User {
        $this->students = $students;
        return $this;
    }

    /**
     * @return UserType
     */
    public function getType(): UserType {
        return $this->type;
    }

    /**
     * @param UserType $type
     * @return User
     */
    public function setType(UserType $type): User {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeString(): string {
        return $this->type->getValue();
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
            ->setStudents(array_map(function(StudentEntity $studentEntity) use($section) {
                return Student::fromEntity($studentEntity, $section);
            }, $userEntity->getStudents()->toArray()));
    }
}