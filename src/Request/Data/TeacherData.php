<?php

namespace App\Request\Data;

use App\Entity\Gender;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TeacherData {

    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $acronym = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $firstname = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $lastname = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $title = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[Serializer\Type('string')]
    private ?string $email = null;

    /**
     * @see Gender
     */
    #[Assert\NotBlank]
    #[Assert\Choice(callback: 'getGenders')]
    #[Serializer\Type('string')]
    private ?string $gender = null;

    #[Serializer\Type("DateTime<'Y-m-d\\TH:i:s'>")]
    private ?DateTime $birthday = null;

    /**
     * @var string[]
     */
    #[Serializer\Type('array<string>')]
    private array $subjects = [ ];

    /**
     * List of external IDs of tags which are added to the user. Note: tags are not synchronized but only added if not present. You have to remove them by hand using the UI.
     *
     * @var string[]
     */
    #[Serializer\Type('array<string>')]
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

    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @return TeacherData
     */
    public function setEmail(?string $email) {
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

    public function getBirthday(): ?DateTime {
        return $this->birthday;
    }

    public function setBirthday(?DateTime $birthday): TeacherData {
        $this->birthday = $birthday;
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
        return array_map(fn(Gender $gender) => $gender->value, Gender::cases());
    }
}