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
    #[ORM\Column(type: 'string')]
    private ?string $name = null;

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
     * @return Collection<Document>
     */
    public function getDocuments(): Collection {
        return $this->documents;
    }

}