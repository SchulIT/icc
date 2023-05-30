<?php

namespace App\Response\Api\V1;

use App\Entity\Gender;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;
use App\Entity\Teacher as TeacherEntity;

class Teacher {

    use UuidTrait;

    #[Serializer\SerializedName('acronym')]
    #[Serializer\Type('string')]
    private ?string $acronym = null;

    #[Serializer\SerializedName('title')]
    #[Serializer\Type('string')]
    private ?string $title = null;

    #[Serializer\SerializedName('firstname')]
    #[Serializer\Type('string')]
    private ?string $firstname = null;

    #[Serializer\SerializedName('lastname')]
    #[Serializer\Type('string')]
    private ?string $lastname = null;

    #[Serializer\SerializedName('gender')]
    #[Serializer\Type('string')]
    #[Serializer\ReadOnly]
    #[Serializer\Accessor(getter: 'getGenderString')]
    private ?Gender $gender = null;

    #[Serializer\SerializedName('email')]
    #[Serializer\Type('string')]
    private ?string $email = null;

    public function getAcronym(): string {
        return $this->acronym;
    }

    public function setAcronym(string $acronym): Teacher {
        $this->acronym = $acronym;
        return $this;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): Teacher {
        $this->title = $title;
        return $this;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): Teacher {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): Teacher {
        $this->lastname = $lastname;
        return $this;
    }

    public function getGender(): Gender {
        return $this->gender;
    }

    public function getGenderString(): string {
        return $this->gender->value;
    }

    public function setGender(Gender $gender): Teacher {
        $this->gender = $gender;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): Teacher {
        $this->email = $email;
        return $this;
    }

    public static function fromEntity(?TeacherEntity $teacherEntity): ?self {
        if($teacherEntity === null) {
            return null;
        }

        return (new self())
            ->setGender($teacherEntity->getGender())
            ->setEmail($teacherEntity->getEmail())
            ->setLastname($teacherEntity->getLastname())
            ->setFirstname($teacherEntity->getFirstname())
            ->setAcronym($teacherEntity->getAcronym())
            ->setTitle($teacherEntity->getTitle())
            ->setUuid($teacherEntity->getUuid());
    }
}