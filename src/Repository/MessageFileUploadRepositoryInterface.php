<?php

namespace App\Repository;

use App\Entity\MessageFile;
use App\Entity\MessageFileUpload;
use App\Entity\User;

interface MessageFileUploadRepositoryInterface {
    public function findOneByFileAndUser(MessageFile $file, User $user): ?MessageFileUpload;

    public function persist(MessageFileUpload $fileUpload): void;
}