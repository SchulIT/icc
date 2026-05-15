<?php

namespace App\Common\View\Filter;

use App\Common\Entity\UserType;
use App\Framework\View\Filter\FilterViewInterface;

class UserTypeFilterView implements FilterViewInterface {

    private bool $handleNull = false;

    /**
     * @param UserType[] $types
     */
    public function __construct(private array $types, private ?UserType $currentType)
    {
    }

    /**
     * @return UserType[]
     */
    public function getTypes(): array {
        return $this->types;
    }

    public function getCurrentType(): ?UserType {
        return $this->currentType;
    }

    public function setCurrentType(?UserType $userType): void {
        $this->currentType = $userType;
    }

    public function getHandleNull(): bool {
        return $this->handleNull;
    }

    public function setHandleNull(bool $handleNull): UserTypeFilterView {
        $this->handleNull = $handleNull;
        return $this;
    }

    public function isEnabled(): bool {
        return count($this->types) > 0;
    }
}