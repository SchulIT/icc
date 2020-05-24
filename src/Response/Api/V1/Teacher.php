<?php

namespace App\Response\Api\V1;

use App\Entity\Gender;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;
use App\Entity\Teacher as TeacherEntity;

class Teacher {

    use UuidTrait;

    /**
     * @Serializer\SerializedName("acronym")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $acronym;

    /**
     * @Serializer\SerializedName("title")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $title;

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
     * @Serializer\SerializedName("title")
     * @Serializer\Type("string")
     * @Serializer\ReadOnly()
     * @Serializer\Accessor(getter="getGenderString")
     *
     * @var Gender
     */
    private $gender;

    /**
     * @Serializer\SerializedName("email")
     * @Serializer\Type("string")
     *
     * @var string|null
     */
    private $email;

    /**
     * @return string
     */
    public function getAcronym(): string {
        return $this->acronym;
    }

    /**
     * @param string $acronym
     * @return Teacher
     */
    public function setAcronym(string $acronym): Teacher {
        $this->acronym = $acronym;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return Teacher
     */
    public function setTitle(?string $title): Teacher {
        $this->title = $title;
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
     * @return Teacher
     */
    public function setFirstname(string $firstname): Teacher {
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
     * @return Teacher
     */
    public function setLastname(string $lastname): Teacher {
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
     * @return string
     */
    public function getGenderString(): string {
        return $this->gender->getValue();
    }

    /**
     * @param Gender $gender
     * @return Teacher
     */
    public function setGender(Gender $gender): Teacher {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return Teacher
     */
    public function setEmail(?string $email): Teacher {
        $this->email = $email;
        return $this;
    }

    public static function fromEntity(?TeacherEntity $teacherEntity): ?self {
        if($teacherEntity === null) {
            return null;
        }

        return (new static())
            ->setGender($teacherEntity->getGender())
            ->setEmail($teacherEntity->getEmail())
            ->setLastname($teacherEntity->getLastname())
            ->setFirstname($teacherEntity->getFirstname())
            ->setAcronym($teacherEntity->getAcronym())
            ->setTitle($teacherEntity->getTitle())
            ->setUuid($teacherEntity->getUuid());
    }
}