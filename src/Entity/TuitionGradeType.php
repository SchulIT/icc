<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Auditable]
class TuitionGradeType {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $displayName = null;

    #[ORM\Column(name: '`values`', type: 'json')]
    #[Assert\Count(min: 1)]
    private array $values = [ ];

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     * @return TuitionGradeType
     */
    public function setDisplayName(?string $displayName): TuitionGradeType {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getValues(): array {
        return $this->values;
    }

    /**
     * @param string[] $values
     * @return TuitionGradeType
     */
    public function setValues(array $values): TuitionGradeType {
        $this->values = $values;
        return $this;
    }
}