<?php

namespace App\Request\Data;

use App\Entity\Gender;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TeacherData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotNull]
    private ?string $id = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $acronym = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $firstname = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $lastname = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $title = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @Serializer\Type("string")
     * @see Gender
     */
    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getGenders')]
    private ?string $gender = null;

    /**
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $subjects = [ ];

    /**
     * List of external IDs of tags which are added to the user. Note: tags are not synchronized but only added if not present. You have to remove them by hand using the UI.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $tags = [ ];

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(?string $id): TeacherData {
        $this->id = $id;
        return $this;
    }

    public function getAcronym(): ?string {
        return $this->acronym;
    }

    public function setAcronym(?string $acronym): TeacherData {
        $this->acronym = $acronym;
        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): TeacherData {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): TeacherData {
        $this->lastname = $lastname;
        return $this;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): TeacherData {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return TeacherData
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getGender(): ?string {
        return $this->gender;
    }

    public function setGender(?string $gender): TeacherData {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getSubjects(): array {
        return $this->subjects;
    }

    /**
     * @param string[] $subjects
     */
    public function setSubjects(array $subjects): TeacherData {
        $this->subjects = $subjects;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): array {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags): TeacherData {
        $this->tags = $tags;
        return $this;
    }

    public static function getGenders() {
        return array_values(Gender::toArray());
    }
}