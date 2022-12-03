<?php

namespace App\Entity;

use DateTime;
use App\Validator\CollectionNotEmpty;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
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
     * @Gedmo\Versioned()
     */
    #[Assert\NotBlank]
    private ?string $title = null;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentCategory", inversedBy="documents")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Assert\NotNull]
    private ?DocumentCategory $category = null;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned()
     */
    #[Assert\NotNull]
    #[Assert\NotBlank]
    private ?string $content = null;

    /**
     * @ORM\ManyToMany(targetEntity="Grade")
     * @ORM\JoinTable(name="document_grades",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     * @var Collection<Grade>
     */
    #[CollectionNotEmpty(propertyPath: 'visibilities')]
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
     * @var Collection<UserTypeEntity>
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
     * @var Collection<User>
     */
    private $authors;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->grades = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->visibilities = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): Document {
        $this->title = $title;
        return $this;
    }

    public function getCategory(): ?DocumentCategory {
        return $this->category;
    }

    public function setCategory(DocumentCategory $category): Document {
        $this->category = $category;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    /**
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

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function addAuthor(Teacher $teacher) {
        $this->authors->add($teacher);
    }

    public function removeAuthor(Teacher $teacher) {
        $this->authors->removeElement($teacher);
    }

    /**
     * @return Collection<User>
     */
    public function getAuthors(): Collection {
        return $this->authors;
    }

}