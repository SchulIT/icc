<?php

namespace App\Chat;

use App\Filesystem\ChatFilesystem;
use App\Repository\ChatRepositoryInterface;

class Cleaner {
    public function __construct(private readonly ChatFilesystem $chatFilesystem, private readonly ChatRepositoryInterface $repository) {

    }

    public function cleanup(): void {
        $this->chatFilesystem->purge();
        $this->repository->removeAll();
    }
}