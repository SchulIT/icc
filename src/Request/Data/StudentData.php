<?php

namespace App\Request\Data;

use App\Entity\Gender;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudentData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $id = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $firstname = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $lastname = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $email = null;

    /**
     * @Serializer\Type("string")
     * @see Gender
     */
    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getGenders')]
    private ?string $gender = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $status = null;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    #[Assert\NotNull]
    private ?DateTime $birthday = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $approvedPrivacyCategories = [ ];

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): StudentData {
        $this->id = $id;
        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): StudentData {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): StudentData {
        $this->email = $email;
        return $this;
    }

    public function setLastname(?string $lastname): StudentData {
        $this->lastname = $lastname;
        return $this;
    }

    public function getGender(): ?string {
        return $this->gender;
    }

    public function setGender(?string $gender): StudentData {
        $this->gender = $gender;
        return $this;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function setStatus(?string $status): StudentData {
        $this->status = $status;
        return $this;
    }

    public function getBirthday(): ?DateTime {
        return $this->birthday;
    }

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
        return array_map(fn(Gender $gender) => $gender->value, Gender::cases());
    }
}