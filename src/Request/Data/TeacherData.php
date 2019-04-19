<?php

namespace App\Request\Data;

use App\Entity\Gender;
use App\Validator\NullOrNotBlank;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TeacherData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotNull()
     * @var string|null
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $acronym;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $firstname;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $lastname;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $title;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getGenders")
     * @see Gender
     * @var string|null
     */
    private $gender;

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return TeacherData
     */
    public function setId(?string $id): TeacherData {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAcronym(): ?string {
        return $this->acronym;
    }

    /**
     * @param string|null $acronym
     * @return TeacherData
     */
    public function setAcronym(?string $acronym): TeacherData {
        $this->acronym = $acronym;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return TeacherData
     */
    public function setFirstname(?string $firstname): TeacherData {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return TeacherData
     */
    public function setLastname(?string $lastname): TeacherData {
        $this->lastname = $lastname;
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
     * @return TeacherData
     */
    public function setTitle(?string $title): TeacherData {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     * @return TeacherData
     */
    public function setGender(?string $gender): TeacherData {
        $this->gender = $gender;
        return $this;
    }

    public static function getGenders() {
        return array_values(Gender::toArray());
    }
}