<?php

namespace App\Entity;

use Stringable;
use App\Validator\NullOrNotBlank;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
#[UniqueEntity(fields: ['acronym'])]
class Teacher implements Stringable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private ?string $acronym = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $firstname = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $lastname = null;

    /**
     * @ORM\Column(type="gender")
     */
    private Gender $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @ORM\ManyToMany(targetEntity="Subject", inversedBy="teachers")
     * @ORM\JoinTable(name="teacher_subjects",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Subject>
     */
    private $subjects;

    /**
     * @ORM\OneToMany(targetEntity="GradeTeacher", mappedBy="teacher")
     * @var Collection<GradeTeacher>
     */
    private $grades;

    /**
     * @ORM\ManyToMany(targetEntity="TeacherTag", cascade={"persist"})
     * @ORM\JoinTable(name="teacher_tags",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<TeacherTag>
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity="Section")
     * @ORM\JoinTable(name="teacher_sections",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Section>
     */
    private $sections;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->setGender(Gender::X());

        $this->subjects = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->sections = new ArrayCollection();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Teacher {
        $this->externalId = $externalId;
        return $this;
    }

    public function getAcronym(): ?string {
        return $this->acronym;
    }

    public function setAcronym(?string $acronym): Teacher {
        $this->acronym = $acronym;
        return $this;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): Teacher {
        $this->title = $title;
        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): Teacher {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): Teacher {
        $this->lastname = $lastname;
        return $this;
    }

    public function getGender(): Gender {
        return $this->gender;
    }

    public function setGender(Gender $gender): Teacher {
        $this->gender = $gender;
        return $this;
    }

    public function setEmail(?string $email): Teacher {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @return Collection<GradeTeacher>
     */
    public function getGrades(): Collection {
        return $this->grades;
    }

    public function addSubject(Subject $subject) {
        $this->subjects->add($subject);
    }

    public function removeSubject(Subject $subject) {
        $this->subjects->removeElement($subject);
    }

    /**
     * @return Collection<Subject>
     */
    public function getSubjects(): Collection {
        return $this->subjects;
    }

    public function addTag(TeacherTag $tag) {
        $this->tags->add($tag);
    }

    public function removeTag(TeacherTag $tag) {
        $this->tags->removeElement($tag);
    }

    /**
     * @return Collection<TeacherTag>
     */
    public function getTags(): Collection {
        return $this->tags;
    }

    public function addSection(Section $section): void {
        $this->sections->add($section);
    }

    public function removeSection(Section $section): void {
        $this->sections->removeElement($section);
    }

    /**
     * @return Collection<Section>
     */
    public function getSections(): Collection {
        return $this->sections;
    }

    public function __toString(): string {
        return (string) $this->getAcronym();
    }
}