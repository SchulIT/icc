<?php

namespace App\Entity;

use Stringable;
use App\Validator\Color;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[UniqueEntity(fields: ['abbreviation'])]
#[ORM\Entity]
class Subject implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $externalId = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $abbreviation = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[ORM\Column(type: 'boolean')]
    private bool $replaceSubjectAbbreviation = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isVisibleGrades = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isVisibleStudents = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isVisibleTeachers = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isVisibleRooms = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isVisibleSubjects = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isVisibleLists = true;

    #[Color]
    #[Assert\Length(min: 7, max: 7)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $color = null;

    /**
     * @var ArrayCollection<Teacher>
     */
    #[ORM\ManyToMany(targetEntity: Teacher::class, mappedBy: 'subjects')]
    private $teachers;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->teachers = new ArrayCollection();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Subject {
        $this->externalId = $externalId;
        return $this;
    }

    public function getAbbreviation(): ?string {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): Subject {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): Subject {
        $this->name = $name;
        return $this;
    }

    public function isReplaceSubjectAbbreviation(): bool {
        return $this->replaceSubjectAbbreviation;
    }

    public function setReplaceSubjectAbbreviation(bool $replaceSubjectAbbreviation): Subject {
        $this->replaceSubjectAbbreviation = $replaceSubjectAbbreviation;
        return $this;
    }

    public function isVisibleGrades(): bool {
        return $this->isVisibleGrades;
    }

    public function setIsVisibleGrades(bool $isVisibleGrades): Subject {
        $this->isVisibleGrades = $isVisibleGrades;
        return $this;
    }

    public function isVisibleStudents(): bool {
        return $this->isVisibleStudents;
    }

    public function setIsVisibleStudents(bool $isVisibleStudents): Subject {
        $this->isVisibleStudents = $isVisibleStudents;
        return $this;
    }

    public function isVisibleTeachers(): bool {
        return $this->isVisibleTeachers;
    }

    public function setIsVisibleTeachers(bool $isVisibleTeachers): Subject {
        $this->isVisibleTeachers = $isVisibleTeachers;
        return $this;
    }

    public function isVisibleRooms(): bool {
        return $this->isVisibleRooms;
    }

    public function setIsVisibleRooms(bool $isVisibleRooms): Subject {
        $this->isVisibleRooms = $isVisibleRooms;
        return $this;
    }

    public function isVisibleSubjects(): bool {
        return $this->isVisibleSubjects;
    }

    public function setIsVisibleSubjects(bool $isVisibleSubjects): Subject {
        $this->isVisibleSubjects = $isVisibleSubjects;
        return $this;
    }

    public function isVisibleLists(): bool {
        return $this->isVisibleLists;
    }

    public function setIsVisibleLists(bool $isVisibleLists): Subject {
        $this->isVisibleLists = $isVisibleLists;
        return $this;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(?string $color): Subject {
        $this->color = $color;
        return $this;
    }

    /**
     * @return ArrayCollection<Teacher>
     */
    public function getTeachers(): ArrayCollection {
        return $this->teachers;
    }

    public function __toString(): string {
        return sprintf('%s [%s]', $this->getName(), $this->getAbbreviation());
    }

}