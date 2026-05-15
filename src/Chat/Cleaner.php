<?php

namespace App\Chat;

use App\Chat\Filesystem\ChatFilesystem;
use App\Chat\Repository\ChatRepositoryInterface;

class Cleaner {
    public function __construct(private readonly ChatFilesystem $chatFilesystem, private readonly ChatRepositoryInterface $repository) {

    }

    public function cleanup(): void {
        $this->chatFilesystem->purge();
        $this->repository->removeAll();
    }
}