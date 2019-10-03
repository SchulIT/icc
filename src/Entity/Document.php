<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(columns={"title"}, flags={"fulltext"}),
 *          @ORM\Index(columns={"content"}, flags={"fulltext"})
 *     }
 * )
 */
class Document {

    /**
     * @ORM\GeneratedValue()
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Gedmo\Slug(fields={"title"})
     * @var string
     */
    private $alias;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
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
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @var string
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity="StudyGroup")
     * @ORM\JoinTable(
     *     name="document_studygroups",
     *     joinColumns={@ORM\JoinColumn(name="page", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="studygroup", onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     * @var Collection<StudyGroup>
     */
    private $studyGroups;

    /**
     * @ORM\OneToMany(targetEntity="DocumentAttachment", mappedBy="document", cascade={"persist"})
     * @var Collection<DocumentAttachment>
     */
    private $attachments;

    /**
     * @ORM\ManyToMany(targetEntity="DocumentVisibility")
     * @ORM\JoinTable(name="document_visibilities",
     *     joinColumns={@ORM\JoinColumn(name="document")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="visibility")}
     * )
     * @var Collection<MessageVisibility>
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
     * @ORM\JoinTable(
     *     name="document_authors",
     *     joinColumns={@ORM\JoinColumn(name="page", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user", onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private $authors;

    public function __construct() {
        $this->studyGroups = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->visibilities = new ArrayCollection();
        $this->authors = new ArrayCollection();
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
    public function getAlias(): string {
        return $this->alias;
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

    public function addStudyGroup(StudyGroup $studyGroup) {
        $this->studyGroups->add($studyGroup);
    }

    public function removeStudyGroup(StudyGroup $studyGroup) {
        $this->studyGroups->removeElement($studyGroup);
    }

    /**
     * @return Collection<StudyGroup>
     */
    public function getStudyGroups(): Collection {
        return $this->studyGroups;
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

    public function addVisibility(MessageVisibility $visibility) {
        $this->visibilities->add($visibility);
    }

    public function removeVisibility(MessageVisibility $visibility) {
        $this->visibilities->removeElement($visibility);
    }

    /**
     * @return Collection<MessageVisibility>
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