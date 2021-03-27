<?php

namespace App\Response\Api\V1;

use App\Entity\Gender;
use JMS\Serializer\Annotation as Serializer;
use App\Entity\Student as StudentEntity;

class Student {

    use UuidTrait;

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
     * @Serializer\SerializedName("gender")
     * @Serializer\Type("string")
     * @Serializer\ReadOnly()
     * @Serializer\Accessor("getGenderString")
     *
     * @var Gender
     */
    private $gender;

    /**
     * @Serializer\SerializedName("email")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $email;

    /**
     * @Serializer\SerializedName("status")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $status;

    /**
     * @Serializer\SerializedName("is_full_aged")
     * @Serializer\Type("bool")
     *
     * @var bool
     */
    private $isFullAged;

    /**
     * @Serializer\SerializedName("grade")
     * @Serializer\Type("App\Response\Api\V1\Grade")
     *
     * @var Grade
     */
    private $grade;

    /**
     * @return string
     */
    public function getFirstname(): string {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Student
     */
    public function setFirstname(string $firstname): Student {
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
     * @return Student
     */
    public function setLastname(string $lastname): Student {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return Gender
     */
    public function getGender(): Gender {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     * @return Student
     */
    public function setGender(Gender $gender): Student {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string
     */
    public function getGenderString(): string {
        return $this->gender->getValue();
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Student
     */
    public function setEmail(string $email): Student {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Student
     */
    public function setStatus(?string $status): Student {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFullAged(): bool {
        return $this->isFullAged;
    }

    /**
     * @param bool $isFullAged
     * @return Student
     */
    public function setIsFullAged(bool $isFullAged): Student {
        $this->isFullAged = $isFullAged;
        return $this;
    }

    /**
     * @return Grade
     */
    public function getGrade(): Grade {
        return $this->grade;
    }

    /**
     * @param Grade $grade
     * @return Student
     */
    public function setGrade(Grade $grade): Student {
        $this->grade = $grade;
        return $this;
    }

    public static function fromEntity(?StudentEntity $studentEntity): ?self {
        if($studentEntity === null) {
            return null;
        }

        return (new self())
            ->setFirstname($studentEntity->getFirstname())
            ->setLastname($studentEntity->getLastname())
            ->setEmail($studentEntity->getEmail())
            ->setGrade(Grade::fromEntity($studentEntity->getGrade()))
            ->setGender($studentEntity->getGender())
            ->setStatus($studentEntity->getStatus())
            ->setUuid($studentEntity->getUuid());
    }
}