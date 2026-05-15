<?php

namespace App\Common\Grouping;

use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Framework\Grouping\GroupInterface;
use App\Framework\Grouping\SortableGroupInterface;

class UserUserTypeGroup implements GroupInterface, SortableGroupInterface {

    /** @var User[] */
    private $users;

    public function __construct(private UserType $userType)
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

    public function getKey() {
        return $this->userType;
    }

    public function addItem($item) {
        $this->users[] = $item;
    }

    public function &getItems(): array {
        return $this->users;
    }
}