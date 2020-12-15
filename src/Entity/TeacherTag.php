<?php

namespace App\Entity;

use App\Validator\Color;
use DH\DoctrineAuditBundle\Annotation\Auditable;
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

    public function __construct() {
        $this->uuid = Uuid::uuid4();
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
}