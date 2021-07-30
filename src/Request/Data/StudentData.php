<?php

namespace App\Request\Data;

use App\Entity\Gender;
use App\Validator\NullOrNotBlank;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudentData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $firstname;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $lastname;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $email;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getGenders")
     * @see Gender
     * @var string|null
     */
    private $gender;

    /**
     * @Serializer\Type("string")
     * @NullOrNotBlank()
     * @var string|null
     */
    private $status;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\NotNull()
     * @var DateTime
     */
    private $birthday;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $approvedPrivacyCategories = [ ];

    /**
     * @return string|null
     */
    public function getId(): ?string {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return StudentData
     */
    public function setId(?string $id): StudentData {
        $this->id = $id;
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
     * @return StudentData
     */
    public function setFirstname(?string $firstname): StudentData {
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
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return StudentData
     */
    public function setEmail(?string $email): StudentData {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string|null $lastname
     * @return StudentData
     */
    public function setLastname(?string $lastname): StudentData {
        $this->lastname = $lastname;
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
     * @return StudentData
     */
    public function setGender(?string $gender): StudentData {
        $this->gender = $gender;
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
     * @return StudentData
     */
    public function setStatus(?string $status): StudentData {
        $this->status = $status;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getBirthday(): ?DateTime {
        return $this->birthday;
    }

    /**
     * @param DateTime|null $birthday
     * @return StudentData
     */
    public function setBirthday(?DateTime $birthday): StudentData {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getApprovedPrivacyCategories() {
        return $this->approvedPrivacyCategories;
    }

    public static function getGenders() {
        return array_values(Gender::toArray());
    }
}