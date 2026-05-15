<?php

namespace App\Message\Repository;

use App\Message\Entity\MessageFile;
use App\Message\Entity\MessageFileUpload;
use App\Common\Entity\User;

interface MessageFileUploadRepositoryInterface {
    public function findOneByFileAndUser(MessageFile $file, User $user): ?MessageFileUpload;

    public function persist(MessageFileUpload $fileUpload): void;

    public function remove(MessageFileUpload $fileUpload): void;
}