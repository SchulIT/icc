<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Auditable]
class TuitionGradeCategory {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $displayName;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    private ?string $comment;

    #[ORM\Column(type: 'integer')]
    #[Gedmo\SortablePosition]
    private int $position;

    #[ORM\Column(type: 'boolean')]
    private bool $isExportable = true;

    #[ORM\ManyToOne(targetEntity: TuitionGradeCatalog::class)]
    #[ORM\JoinColumn]
    #[Assert\NotNull]
    private ?TuitionGradeCatalog $gradeType;

    /**
     * @var Collection<Tuition>
     */
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
     * @return string|null
     */
    public function getComment(): ?string {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return TuitionGradeCategory
     */
    public function setComment(?string $comment): TuitionGradeCategory {
        $this->comment = $comment;
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
     * @return bool
     */
    public function isExportable(): bool {
        return $this->isExportable;
    }

    /**
     * @param bool $isExportable
     * @return TuitionGradeCategory
     */
    public function setIsExportable(bool $isExportable): TuitionGradeCategory {
        $this->isExportable = $isExportable;
        return $this;
    }

    /**
     * @return TuitionGradeCatalog|null
     */
    public function getGradeType(): ?TuitionGradeCatalog {
        return $this->gradeType;
    }

    /**
     * @param TuitionGradeCatalog|null $gradeType
     * @return TuitionGradeCategory
     */
    public function setGradeType(?TuitionGradeCatalog $gradeType): TuitionGradeCategory {
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