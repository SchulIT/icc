<?php

namespace App\Entity;

use App\Validator\Color;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 * @UniqueEntity(fields={"externalId"})
 */
class TeacherTag {

    public const GradeTeacherTagUuid = '89274ce2-3f85-48c8-890e-1aea0e08d21d';
    public const SubstituteGradeTeacherTagUuid = '8660bfe4-6edf-44b5-99f6-5810b948c0ae';

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, length=32)
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=7)
     * @Color()
     * @Assert\NotBlank()
     */
    private $color;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="teacher_tag_visibilities",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<UserTypeEntity>
     */
    private $visibilities;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->visibilities = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getExternalId() {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return TeacherTag
     */
    public function setExternalId($externalId) {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return TeacherTag
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * @param string|null $color
     * @return TeacherTag
     */
    public function setColor($color) {
        $this->color = $color;
        return $this;
    }

    public function addVisibility(UserTypeEntity $userType): void {
        $this->visibilities->add($userType);
    }

    public function removeVisibility(UserTypeEntity $userType): void {
        $this->visibilities->removeElement($userType);
    }

    public function getVisibilities(): Collection {
        return $this->visibilities;
    }

    public static function getGradeTeacherTag(): self {
        $tag = new self();
        $tag->uuid = Uuid::fromString(self::GradeTeacherTagUuid);

        return $tag;
    }

    public static function getSubstituteGradeTeacherTag(): self {
        $tag = new self();
        $tag->uuid = Uuid::fromString(self::SubstituteGradeTeacherTagUuid);

        return $tag;
    }
}