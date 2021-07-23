<?php

namespace App\Book\Export;

use JMS\Serializer\Annotation as Serializer;

class Student {

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
     * @Serializer\SerializedName("identifier")
     * @var string|null
     */
    private $identifier;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("firstname")
     * @var string|null
     */
    private $firstname;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("lastname")
     * @var string|null
     */
    private $lastname;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("grade")
     * @var string|null
     */
    private $grade;

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Student
     */
    public function setId(string $id): Student {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     * @return Student
     */
    public function setIdentifier(?string $identifier): Student {
        $this->identifier = $identifier;
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
     * @return Student
     */
    public function setFirstname(?string $firstname): Student {
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
     * @return Student
     */
    public function setLastname(?string $lastname): Student {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @param string|null $grade
     * @return Student
     */
    public function setGrade(?string $grade): Student {
        $this->grade = $grade;
        return $this;
    }
}