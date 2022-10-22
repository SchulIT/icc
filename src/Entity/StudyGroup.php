<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={
 *      @ORM\UniqueConstraint(fields={"section", "externalId"})
 * })
 * @Auditable()
 */
class StudyGroup implements Stringable {

    use IdTrait;
    use UuidTrait;
    use SectionAwareTrait;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="string")
     */
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @ORM\Column(type="study_group_type")
     */
    #[Assert\NotNull]
    private StudyGroupType $type;

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(name="study_group_grades",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn()}
     * )
     * @var Collection<Grade>
     */
    private $grades;

    /**
     * @ORM\OneToMany(targetEntity="StudyGroupMembership", mappedBy="studyGroup")
     * @var Collection<StudyGroupMembership>
     */
    private $memberships;

    /**
     * @ORM\OneToMany(targetEntity="Tuition", mappedBy="studyGroup")
     * @var Collection<Tuition>
     */
    private $tuitions;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->type = StudyGroupType::Course();
        $this->grades = new ArrayCollection();
        $this->memberships = new ArrayCollection();
        $this->tuitions = new ArrayCollection();
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): StudyGroup {
        $this->externalId = $externalId;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): StudyGroup {
        $this->name = $name;
        return $this;
    }

    public function getType(): StudyGroupType {
        return $this->type;
    }

    public function setType(StudyGroupType $type): StudyGroup {
        $this->type = $type;
        return $this;
    }

    public function addGrade(Grade $grade) {
        $this->grades->add($grade);
    }

    public function removeGrade(Grade $grade) {
        $this->grades->removeElement($grade);
    }

    /**
     * @return Collection<Grade>
     */
    public function getGrades(): Collection {
        return $this->grades;
    }

    /**
     * @return Collection<StudyGroupMembership>
     */
    public function getMemberships(): Collection {
        return $this->memberships;
    }

    /**
     * @return Collection<Tuition>
     */
    public function getTuitions(): Collection {
        return $this->tuitions;
    }

    public function __toString(): string {
        return sprintf('%s: %s', implode(', ', $this->getGrades()->toArray()), $this->getName());
    }
}