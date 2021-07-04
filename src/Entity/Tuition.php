<?php

namespace App\Entity;

use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @UniqueEntity(fields={"section", "externalId"})
 */
class Tuition {

    use IdTrait;
    use UuidTrait;
    use SectionAwareTrait;

    /**
     * @ORM\Column(type="string")
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     *
     * @var string|null
     */
    private $displayName = null;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var Subject|null
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher|null
     */
    private $teacher;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable(name="tuition_additional_teachers",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private $additionalTeachers;

    /**
     * @ORM\ManyToOne(targetEntity="StudyGroup", inversedBy="tuitions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var StudyGroup|null
     */
    private $studyGroup;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->additionalTeachers = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return Tuition
     */
    public function setExternalId(?string $externalId): Tuition {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Tuition
     */
    public function setName(?string $name): Tuition {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     * @return Tuition
     */
    public function setDisplayName(?string $displayName): Tuition {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return Subject|null
     */
    public function getSubject(): ?Subject {
        return $this->subject;
    }

    /**
     * @param Subject|null $subject
     * @return Tuition
     */
    public function setSubject(?Subject $subject): Tuition {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return Tuition
     */
    public function setTeacher(?Teacher $teacher): Tuition {
        $this->teacher = $teacher;
        return $this;
    }

    public function addAdditionalTeacher(Teacher $teacher) {
        $this->additionalTeachers->add($teacher);
    }

    public function removeAdditionalTeacher(Teacher $teacher) {
        $this->additionalTeachers->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getAdditionalTeachers(): Collection {
        return $this->additionalTeachers;
    }

    /**
     * @return StudyGroup|null
     */
    public function getStudyGroup(): ?StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @param StudyGroup|null $studyGroup
     * @return Tuition
     */
    public function setStudyGroup(?StudyGroup $studyGroup): Tuition {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers() {
        if($this->getTeacher() === null) {
            return $this->getAdditionalTeachers()->toArray();
        }

        return array_merge(
            [ $this->getTeacher() ],
            $this->getAdditionalTeachers()->toArray()
        );
    }

    public function __toString() {
        return sprintf('%s [%s]', $this->getStudyGroup(), $this->getSubject());
    }
}