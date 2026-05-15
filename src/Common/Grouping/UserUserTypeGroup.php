<?php

namespace App\Common\Grouping;

use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

/**
 * @implements SortableGroupInterface<UserType, User>
 */
class UserUserTypeGroup implements SortableGroupInterface {

    /** @var User[] */
    private array $users;

    public function __construct(private readonly UserType $userType)
    {
    }

    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array {
        return $this->users;
    }

    public function getKey(): UserType {
        return $this->userType;
    }

    public function addItem($item): void {
        $this->users[] = $item;
    }

    public function &getItems(): array {
        return $this->users;
    }
}