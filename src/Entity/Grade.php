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
class Grade implements Stringable {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $externalId = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $name = null;

    /**
     * @var ArrayCollection<GradeMembership>
     */
    #[ORM\OneToMany(mappedBy: 'grade', targetEntity: GradeMembership::class)]
    private $memberships;

    /**
     * @var ArrayCollection<GradeTeacher>
     */
    #[ORM\OneToMany(mappedBy: 'grade', targetEntity: GradeTeacher::class, cascade: ['persist'])]
    private $teachers;

    #[ORM\Column(type: 'boolean')]
    private bool $allowCollapse = true;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->memberships = new ArrayCollection();
        $this->teachers = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): Grade {
        $this->externalId = $externalId;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): Grade {
        $this->name = $name;
        return $this;
    }

    public function addMemebership(GradeMembership $membership): void {
        $membership->setGrade($this);
        $this->memberships->add($membership);
    }

    public function removeMembership(GradeMembership $membership): void {
        $this->memberships->removeElement($membership);
    }

    /**
     * @return Collection<GradeMembership>
     */
    public function getMemberships(): Collection {
        return $this->memberships;
    }

    public function addTeacher(GradeTeacher $teacher) {
        $teacher->setGrade($this);
        $this->teachers->add($teacher);
    }

    public function removeTeacher(GradeTeacher $teacher) {
        $this->teachers->removeElement($teacher);
    }

    /**
     * @return Collection<GradeTeacher>
     */
    public function getTeachers(): Collection {
        return $this->teachers;
    }

    public function allowCollapse(): bool {
        return $this->allowCollapse;
    }

    public function setAllowCollapse(bool $allowCollapse): Grade {
        $this->allowCollapse = $allowCollapse;
        return $this;
    }

    public function __toString(): string {
        return (string) $this->getName();
    }

}