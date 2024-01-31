<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Student {

    use UuidTrait;

    /**
     * The ID which is specified as ID when importing students.
     */
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('id')]
    private ?string $id = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('firstname')]
    private ?string $firstname = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('lastname')]
    private ?string $lastname = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('grade')]
    private ?string $grade = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('membership_type')]
    private ?string $membershipType = null;

    public function getId(): string {
        return $this->id;
    }

    public function setId(string $id): Student {
        $this->id = $id;
        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): Student {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): Student {
        $this->lastname = $lastname;
        return $this;
    }

    public function getGrade(): ?string {
        return $this->grade;
    }

    public function setGrade(?string $grade): Student {
        $this->grade = $grade;
        return $this;
    }

    public function getMembershipType(): ?string {
        return $this->membershipType;
    }

    public function setMembershipType(?string $membershipType): Student {
        $this->membershipType = $membershipType;
        return $this;
    }
}