<?php

namespace App\View\Filter;

use App\Grouping\Grouper;
use App\Repository\UserRepositoryInterface;
use App\Sorting\Sorter;

class UserFilter {
    private $repository;
    private $grouper;
    private $sorter;

    public function __construct(UserRepositoryInterface $userRepository, Grouper $grouper, Sorter $sorter) {
        $this->repository = $userRepository;
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }
}