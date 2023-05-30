<?php

namespace App\Response\Api\V1;

use App\Entity\Gender;
use App\Entity\Section;
use JMS\Serializer\Annotation as Serializer;
use App\Entity\Student as StudentEntity;

class Student {

    use UuidTrait;

    #[Serializer\SerializedName('firstname')]
    #[Serializer\Type('string')]
    private ?string $firstname = null;

    #[Serializer\SerializedName('lastname')]
    #[Serializer\Type('string')]
    private ?string $lastname = null;

    #[Serializer\SerializedName('gender')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnly]
    #[Serializer\Accessor('getGenderString')]
    private ?Gender $gender = null;

    #[Serializer\SerializedName('email')]
    #[Serializer\Type('string')]
    private ?string $email = null;

    #[Serializer\SerializedName('status')]
    #[Serializer\Type('string')]
    private ?string $status = null;

    #[Serializer\SerializedName('is_full_aged')]
    #[Serializer\Type('bool')]
    private ?bool $isFullAged = null;

    #[Serializer\SerializedName('grade')]
    #[Serializer\Type(Grade::class)]
    private ?Grade $grade = null;

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): Student {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): Student {
        $this->lastname = $lastname;
        return $this;
    }

    public function getGender(): Gender {
        return $this->gender;
    }

    public function setGender(Gender $gender): Student {
        $this->gender = $gender;
        return $this;
    }

    public function getGenderString(): string {
        return $this->gender->value;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): Student {
        $this->email = $email;
        return $this;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus(?string $status): Student {
        $this->status = $status;
        return $this;
    }

    public function isFullAged(): bool {
        return $this->isFullAged;
    }

    public function setIsFullAged(bool $isFullAged): Student {
        $this->isFullAged = $isFullAged;
        return $this;
    }

    public function getGrade(): ?Grade {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): Student {
        $this->grade = $grade;
        return $this;
    }

    public static function fromEntity(?StudentEntity $studentEntity, ?Section $section = null): ?self {
        if($studentEntity === null) {
            return null;
        }

        return (new self())
            ->setFirstname($studentEntity->getFirstname())
            ->setLastname($studentEntity->getLastname())
            ->setEmail($studentEntity->getEmail())
            ->setGrade(Grade::fromEntity($studentEntity->getGrade($section)))
            ->setGender($studentEntity->getGender())
            ->setStatus($studentEntity->getStatus())
            ->setUuid($studentEntity->getUuid());
    }
}