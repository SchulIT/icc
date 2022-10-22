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

/**
 * @ORM\Entity()
 * @Auditable()
 */
#[UniqueEntity(fields: ['section', 'externalId'])]
class Tuition implements Stringable {

    use IdTrait;
    use UuidTrait;
    use SectionAwareTrait;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $displayName = null;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Assert\NotNull]
    private ?Subject $subject = null;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable(name="tuition_teachers",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private $teachers;

    /**
     * @ORM\ManyToOne(targetEntity="StudyGroup", inversedBy="tuitions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Assert\NotNull]
    private ?StudyGroup $studyGroup = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->teachers = new ArrayCollection();
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

    public function __toString(): string {
        return sprintf('%s [%s]', $this->getStudyGroup(), $this->getSubject());
    }
}