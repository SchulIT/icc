<?php

namespace App\View\Filter;

use App\Entity\UserType;

class UserTypeFilterView implements FilterViewInterface {

    /** @var UserType[] */
    private $types;

    /** @var UserType|null  */
    private $currentType;

    /** @var bool */
    private $handleNull = false;

    public function __construct(array $types, ?UserType $type) {
        $this->types = $types;
        $this->currentType = $type;
    }

    /**
     * @return UserType[]
     */
    public function getTypes(): array {
        return $this->types;
    }

    /**
     * @return UserType|null
     */
    public function getCurrentType(): ?UserType {
        return $this->currentType;
    }

    /**
     * @param UserType|null $userType
     */
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