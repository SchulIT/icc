<?php

namespace App\Entity;

use Stringable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
#[ORM\UniqueConstraint(fields: ['section', 'externalId'])]
class StudyGroup implements Stringable {

    use IdTrait;
    use UuidTrait;
    use SectionAwareTrait;

    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(type: 'string')]
    private ?string $externalId = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[Assert\NotNull]
    #[ORM\Column(type: 'string', enumType: StudyGroupType::class)]
    private StudyGroupType $type;

    /**
     * @var Collection<Grade>
     */
    #[ORM\JoinTable(name: 'study_group_grades')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn]
    #[ORM\ManyToMany(targetEntity: Grade::class)]
    private $grades;

    /**
     * @var Collection<StudyGroupMembership>
     */
    #[ORM\OneToMany(mappedBy: 'studyGroup', targetEntity: StudyGroupMembership::class)]
    private $memberships;

    /**
     * @var Collection<Tuition>
     */
    #[ORM\OneToMany(mappedBy: 'studyGroup', targetEntity: Tuition::class)]
    private $tuitions;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->type = StudyGroupType::Course;
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