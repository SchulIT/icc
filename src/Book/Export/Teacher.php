<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Teacher {

    /**
     * The ID which is specified as ID when importing students.
     */
    #[Serializer\Type('string')]
    #[Serializer\SerializedName('id')]
    private ?string $id = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('acronym')]
    private ?string $acronym = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('firstname')]
    private ?string $firstname = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('lastname')]
    private ?string $lastname = null;

    #[Serializer\Type('string')]
    #[Serializer\SerializedName('title')]
    private ?string $title = null;

    public function getId(): string {
        return $this->id;
    }

    public function setId(string $id): Teacher {
        $this->id = $id;
        return $this;
    }

    public function getAcronym(): string {
        return $this->acronym;
    }

    public function setAcronym(string $acronym): Teacher {
        $this->acronym = $acronym;
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

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): Teacher {
        $this->title = $title;
        return $this;
    }
}