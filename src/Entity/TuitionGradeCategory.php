<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity]
#[Auditable]
class TuitionGradeCategory {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[NotBlank]
    private ?string $displayName;

    #[ORM\Column(type: 'integer')]
    #[Gedmo\SortablePosition]
    private int $position;

    #[ORM\ManyToOne(targetEntity: TuitionGradeType::class)]
    #[ORM\JoinColumn]
    #[NotNull]
    private ?TuitionGradeType $gradeType;

    #[ORM\ManyToMany(targetEntity: Tuition::class, inversedBy: 'gradeCategories')]
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    private Collection $tuitions;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->tuitions = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     * @return TuitionGradeCategory
     */
    public function setDisplayName(?string $displayName): TuitionGradeCategory {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int {
        return $this->position;
    }

    /**
     * @param int $position
     * @return TuitionGradeCategory
     */
    public function setPosition(int $position): TuitionGradeCategory {
        $this->position = $position;
        return $this;
    }

    /**
     * @return TuitionGradeType|null
     */
    public function getGradeType(): ?TuitionGradeType {
        return $this->gradeType;
    }

    /**
     * @param TuitionGradeType|null $gradeType
     * @return TuitionGradeCategory
     */
    public function setGradeType(?TuitionGradeType $gradeType): TuitionGradeCategory {
        $this->gradeType = $gradeType;
        return $this;
    }

    public function addTuition(Tuition $tuition): void {
        $this->tuitions->add($tuition);
    }

    public function removeTuition(Tuition $tuition): void {
        $this->tuitions->removeElement($tuition);
    }

    /**
     * @return Collection<Tuition>
     */
    public function getTuitions(): Collection {
        return $this->tuitions;
    }
}