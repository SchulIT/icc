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
     * @Gedmo\Versioned()
     */
    #[Assert\NotBlank]
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $icon = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isOnline = true;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private ?\DateTime $createdAt = null;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private ?\DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned()
     */
    #[Assert\NotBlank]
    private ?string $content = null;

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
     */
    private int $left;

    /**
     * @Gedmo\TreeLevel()
     * @ORM\Column(type="integer")
     */
    private int $level;

    /**
     * @Gedmo\TreeRight()
     * @ORM\Column(type="integer", name="`right`")
     */
    private int $right;

    /**
     * @Gedmo\TreeRoot()
     * @ORM\ManyToOne(targetEntity="WikiArticle")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?\App\Entity\WikiArticle $root = null;

    /**
     * @Gedmo\TreeParent()
     * @ORM\ManyToOne(targetEntity="WikiArticle", inversedBy="children")
     * @ORM\JoinColumn(name="`parent`", onDelete="CASCADE")
     */
    private ?\App\Entity\WikiArticle $parent = null;

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

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): WikiArticle {
        $this->title = $title;
        return $this;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $icon): WikiArticle {
        $this->icon = $icon;
        return $this;
    }

    public function isOnline(): bool {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): WikiArticle {
        $this->isOnline = $isOnline;
        return $this;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    public function getContent(): ?string {
        return $this->content;
    }

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

    public function getVisibilities(): Collection {
        return $this->visibilities;
    }

    public function getRoot(): ?WikiArticle {
        return $this->root;
    }

    public function getParent(): ?WikiArticle {
        return $this->parent;
    }

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