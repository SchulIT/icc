<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[UniqueEntity(fields: ['section', 'externalId'])]
#[ORM\Entity]
class Tuition implements Stringable {

    use IdTrait;
    use UuidTrait;
    use SectionAwareTrait;

    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $externalId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $displayName = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isBookEnabled = true;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Subject $subject = null;

    /**
     * @var Collection<Teacher>
     */
    #[ORM\JoinTable(name: 'tuition_teachers')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: Teacher::class)]
    private $teachers;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: StudyGroup::class, inversedBy: 'tuitions')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?StudyGroup $studyGroup = null;

    /**
     * @var Collection<TuitionGradeCategory>
     */
    #[ORM\ManyToMany(targetEntity: TuitionGradeCategory::class, mappedBy: 'tuitions')]
    private Collection $gradeCategories;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->teachers = new ArrayCollection();
        $this->gradeCategories = new ArrayCollection();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): Tuition {
        $this->externalId = $externalId;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): Tuition {
        $this->name = $name;
        return $this;
    }

    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): Tuition {
        $this->displayName = $displayName;
        return $this;
    }

    public function isBookEnabled(): bool {
        return $this->isBookEnabled;
    }

    public function setIsBookEnabled(bool $isBookEnabled): Tuition {
        $this->isBookEnabled = $isBookEnabled;
        return $this;
    }

    public function getSubject(): ?Subject {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): Tuition {
        $this->subject = $subject;
        return $this;
    }

    public function addTeacher(Teacher $teacher) {
        $this->teachers->add($teacher);
    }

    public function removeTeacher(Teacher $teacher) {
        $this->teachers->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getTeachers(): Collection {
        return $this->teachers;
    }

    public function getStudyGroup(): ?StudyGroup {
        return $this->studyGroup;
    }

    public function setStudyGroup(?StudyGroup $studyGroup): Tuition {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    /**
     * @return Collection<TuitionGradeCategory>
     */
    public function getGradeCategories(): Collection {
        return $this->gradeCategories;
    }

    public function __toString(): string {
        return sprintf('%s (%s) [%s]', $this->getStudyGroup(), $this->getSubject(), $this->getSection()->getDisplayName());
    }
}