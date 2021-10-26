<?php

namespace App\Entity;

use App\Validator\CollectionNotEmpty;
use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(columns={"title"}, flags={"fulltext"}),
 *          @ORM\Index(columns={"content"}, flags={"fulltext"})
 *     }
 * )
 * @Gedmo\Loggable()
 */
class Document {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Gedmo\Versioned()
     * @var string
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentCategory", inversedBy="documents")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var DocumentCategory
     */
    private $category;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned()
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(name="document_grades",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     * @CollectionNotEmpty(propertyPath="visibilities")
     * @var Collection<Grade>
     */
    private $grades;

    /**
     * @ORM\OneToMany(targetEntity="DocumentAttachment", mappedBy="document", cascade={"persist"})
     * @ORM\OrderBy({"filename"="asc"})
     * @var Collection<DocumentAttachment>
     */
    private $attachments;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="document_visibilities",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<UserTypeEntity>
     */
    private $visibilities;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Gedmo\Timestampable(on="create")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="document_authors",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private $authors;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->grades = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->visibilities = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return Document
     */
    public function setTitle(?string $title): Document {
        $this->title = $title;
        return $this;
    }

    /**
     * @return DocumentCategory|null
     */
    public function getCategory(): ?DocumentCategory {
        return $this->category;
    }

    /**
     * @param DocumentCategory $category
     * @return Document
     */
    public function setCategory(DocumentCategory $category): Document {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return Document
     */
    public function setContent(?string $content) {
        $this->content = $content;
        return $this;
    }

    public function addGrade(Grade $grade) {
        $this->grades->add($grade);
    }

    public function removeGrade(Grade $grade) {
        $this->grades->removeElement($grade);
    }

    /**
     * @return Collection<Grade>
     */
    public function getGrades(): Collection {
        return $this->grades;
    }

    public function addAttachment(DocumentAttachment $attachment) {
        if($attachment->getDocument() === $this) {
            // Do not read already existing attachments (seems to fix a bug with VichUploaderBundle https://github.com/dustin10/VichUploaderBundle/issues/842)
            return;
        }

        $attachment->setDocument($this);
        $this->attachments->add($attachment);
    }

    public function removeAttachment(DocumentAttachment $attachment) {
        $this->attachments->removeElement($attachment);
    }

    /**
     * @return Collection<DocumentAttachment>
     */
    public function getAttachments(): Collection {
        return $this->attachments;
    }

    public function addVisibility(UserTypeEntity $visibility) {
        $this->visibilities->add($visibility);
    }

    public function removeVisibility(UserTypeEntity $visibility) {
        $this->visibilities->removeElement($visibility);
    }

    /**
     * @return Collection<UserTypeEntity>
     */
    public function getVisibilities(): Collection {
        return $this->visibilities;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime {
        return $this->updatedAt;
    }

    public function addAuthor(Teacher $teacher) {
        $this->authors->add($teacher);
    }

    public function removeAuthor(Teacher $teacher) {
        $this->authors->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getAuthors(): Collection {
        return $this->authors;
    }

}