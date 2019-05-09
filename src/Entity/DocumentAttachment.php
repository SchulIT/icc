<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Vich\Uploadable()
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
     * @Vich\UploadableField(mapping="documents", fileNameProperty="filename")
     */
    private $file;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     * @var string
     */
    private $filename;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $size;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeImmutable
     */
    private $updatedAt;

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
     * @return File|null
     */
    public function getFile(): ?File {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return MessageAttachment
     */
    public function setFile(?File $file = null): DocumentAttachment {
        $this->file = $file;

        if($file !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return MessageAttachment
     */
    public function setFilename(?string $filename): DocumentAttachment {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int {
        return $this->size;
    }

    /**
     * @param int|null $size
     * @return MessageAttachment
     */
    public function setSize(?int $size): DocumentAttachment {
        $this->size = $size;
        return $this;
    }
}