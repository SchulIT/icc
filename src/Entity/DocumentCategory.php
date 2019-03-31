<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class DocumentCategory {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

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
        $this->documents = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return DocumentCategory
     */
    public function setName(string $name): DocumentCategory {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection<Document>
     */
    public function getDocuments(): ArrayCollection {
        return $this->documents;
    }

}