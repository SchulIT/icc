<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class DocumentAttachment {

    /**
     * @ORM\GeneratedValue()
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Document
     */
    private $document;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     * @var string
     */
    private $filename;

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return Document|null
     */
    public function getDocument(): ?Document {
        return $this->document;
    }

    /**
     * @param Document $document
     * @return DocumentAttachment
     */
    public function setDocument(Document $document): DocumentAttachment {
        $this->document = $document;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return DocumentAttachment
     */
    public function setFilename(string $filename): DocumentAttachment {
        $this->filename = $filename;
        return $this;
    }
}