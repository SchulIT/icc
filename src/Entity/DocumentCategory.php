<?php

namespace App\Entity;

use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class DocumentCategory {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="category")
     * @var ArrayCollection<Document>
     */
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

    /**
     * @param string|null $name
     * @return DocumentCategory
     */
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