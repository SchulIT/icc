<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class DocumentCategory {

    use IdTrait;
    use UuidTrait;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    private ?string $icon = null;

    /**
     * @var ArrayCollection<Document>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Document::class)]
    private $documents;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->documents = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): DocumentCategory {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return DocumentCategory
     */
    public function setIcon(?string $icon): DocumentCategory {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return Collection<Document>
     */
    public function getDocuments(): Collection {
        return $this->documents;
    }

}