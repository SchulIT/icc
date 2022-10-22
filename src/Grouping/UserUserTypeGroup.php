<?php

namespace App\Grouping;

use App\Entity\User;
use App\Entity\UserType;

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