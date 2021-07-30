<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Teacher {

    /**
     * The ID which is specified as ID when importing students.
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("id")
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("acronym")
     * @var string
     */
    private $acronym;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("firstname")
     * @var string
     */
    private $firstname;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("lastname")
     * @var string
     */
    private $lastname;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("title")
     * @var string|null
     */
    private $title;

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Teacher
     */
    public function setId(string $id): Teacher {
        $this->id = $id;
        return $this;
    }

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
}