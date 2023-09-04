<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Loggable;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[Gedmo\Tree(type: 'nested')]
#[Gedmo\Loggable]
#[ORM\Entity]
#[ORM\Table(name: 'wiki')]
#[ORM\Index(columns: ['title'], flags: ['fulltext'])]
#[ORM\Index(columns: ['content'], flags: ['fulltext'])]
class WikiArticle {

    use IdTrait;
    use UuidTrait;

    #[Gedmo\Versioned]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string')]
    private ?string $title = null;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isOnline = true;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    private ?DateTime $updatedAt = null;

    #[Gedmo\Versioned]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    /**
     * @var ArrayCollection<UserTypeEntity>
     */
    #[ORM\JoinTable(name: 'wiki_article_visibilities')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: UserTypeEntity::class)]
    private $visibilities;

    #[Gedmo\TreeLeft]
    #[ORM\Column(name: '`left`', type: 'integer')]
    private int $left;

    #[Gedmo\TreeLevel]
    #[ORM\Column(type: 'integer')]
    private int $level;

    #[Gedmo\TreeRight]
    #[ORM\Column(name: '`right`', type: 'integer')]
    private int $right;

    #[Gedmo\TreeRoot]
    #[ORM\ManyToOne(targetEntity: WikiArticle::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?WikiArticle $root = null;

    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(targetEntity: WikiArticle::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent', onDelete: 'CASCADE')]
    private ?WikiArticle $parent = null;

    /**
     * @var Collection<WikiArticle>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: WikiArticle::class)]
    #[ORM\OrderBy(['title' => 'ASC'])]
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