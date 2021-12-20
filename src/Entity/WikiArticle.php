<?php

namespace App\Entity;

use DateTime;
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
 *     name="wiki",
 *     indexes={
 *         @ORM\Index(columns={"title"}, flags={"fulltext"}),
 *         @ORM\Index(columns={"content"}, flags={"fulltext"})
 *     }
 * )
 * @Gedmo\Tree(type="nested")
 * @Gedmo\Loggable()
 */
class WikiArticle {

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
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $icon;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isOnline = true;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var DateTime|null
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @var DateTime|null
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned()
     * @Assert\NotBlank()
     * @var string
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="wiki_article_visibilities",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<UserTypeEntity>
     */
    private $visibilities;

    /**
     * @Gedmo\TreeLeft()
     * @ORM\Column(type="integer", name="`left`")
     * @var int
     */
    private $left;

    /**
     * @Gedmo\TreeLevel()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $level;

    /**
     * @Gedmo\TreeRight()
     * @ORM\Column(type="integer", name="`right`")
     * @var int
     */
    private $right;

    /**
     * @Gedmo\TreeRoot()
     * @ORM\ManyToOne(targetEntity="WikiArticle")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var WikiArticle|null
     */
    private $root;

    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="WikiArticle", inversedBy="children")
     * @ORM\JoinColumn(name="`parent`", onDelete="CASCADE")
     * @var WikiArticle|null
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="WikiArticle", mappedBy="parent")
     * @ORM\OrderBy({"title" = "ASC"})
     * @var Collection<WikiArticle>
     */
    private $children;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->visibilities = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return WikiArticle
     */
    public function setTitle(?string $title): WikiArticle {
        $this->title = $title;
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
     * @return WikiArticle
     */
    public function setIcon(?string $icon): WikiArticle {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOnline(): bool {
        return $this->isOnline;
    }

    /**
     * @param bool $isOnline
     * @return WikiArticle
     */
    public function setIsOnline(bool $isOnline): WikiArticle {
        $this->isOnline = $isOnline;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return WikiArticle
     */
    public function setContent(?string $content): WikiArticle {
        $this->content = $content;
        return $this;
    }

    public function addVisibility(UserTypeEntity $visibility) {
        $this->visibilities->add($visibility);
    }

    public function removeVisibility(UserTypeEntity $visibility) {
        $this->visibilities->removeElement($visibility);
    }

    /**
     * @return Collection
     */
    public function getVisibilities(): Collection {
        return $this->visibilities;
    }

    /**
     * @return WikiArticle|null
     */
    public function getRoot(): ?WikiArticle {
        return $this->root;
    }

    /**
     * @return WikiArticle|null
     */
    public function getParent(): ?WikiArticle {
        return $this->parent;
    }

    /**
     * @param WikiArticle|null $parent
     * @return WikiArticle
     */
    public function setParent(?WikiArticle $parent): WikiArticle {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<WikiArticle>
     */
    public function getChildren(): Collection {
        return $this->children;
    }
}