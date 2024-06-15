<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Auditable]
class TuitionGradeCatalog {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $displayName = null;

    /**
     * @var Collection<TuitionGradeCatalogGrade>
     */
    #[ORM\OneToMany(mappedBy: 'catalog', targetEntity: TuitionGradeCatalogGrade::class, cascade: ['persist'])]
    #[Assert\Count(min: 1)]
    private Collection $grades;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->grades = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     * @return TuitionGradeCatalog
     */
    public function setDisplayName(?string $displayName): TuitionGradeCatalog {
        $this->displayName = $displayName;
        return $this;
    }

    public function addGrade(TuitionGradeCatalogGrade $grade): void {
        $grade->setCatalog($this);
        $this->grades->add($grade);
    }

    public function removeGrade(TuitionGradeCatalogGrade $grade): void {
        $this->grades->removeElement($grade);
    }

    /**
     * @return Collection<TuitionGradeCatalogGrade>
     */
    public function getGrades(): Collection {
        return $this->grades;
    }
}