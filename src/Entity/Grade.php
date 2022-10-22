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
 * @Auditable()
 */
class Grade implements Stringable {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $externalId = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity="GradeMembership", mappedBy="grade")
     * @var ArrayCollection<GradeMembership>
     */
    private $memberships;

    /**
     * @ORM\OneToMany(targetEntity="GradeTeacher", mappedBy="grade", cascade={"persist"})
     * @var ArrayCollection<GradeTeacher>
     */
    private $teachers;

    /**
     * @ORM\Column(type="boolean")
     */
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

    /**
     * @return Collection<GradeMembership>
     */
    public function getMemberships(): Collection {
        return $this->memberships;
    }

    public function addTeacher(GradeTeacher $teacher) {
        $this->teachers->add($teacher);
    }

    public function removeTeacher(GradeTeacher $teacher) {
        $this->teachers->removeElement($teacher);
    }

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