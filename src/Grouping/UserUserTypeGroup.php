<?php

namespace App\Grouping;

use App\Entity\User;
use App\Entity\UserType;

class UserUserTypeGroup implements GroupInterface, SortableGroupInterface {

    /** @var UserType */
    private $userType;

    /** @var User[] */
    private $users;

    public function __construct(UserType $userType) {
        $this->userType = $userType;
    }

    /**
     * @return UserType
     */
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