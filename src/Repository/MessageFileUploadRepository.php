<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\MessageFile;
use App\Entity\MessageFileUpload;
use App\Entity\User;

class MessageFileUploadRepository extends AbstractRepository implements MessageFileUploadRepositoryInterface {

    public function findAll(Message $message): array {
        $qb = $this->em->createQueryBuilder();


        return $this->em->getRepository(MessageFileUpload::class)
            ->findAll();
    }

    public function findOneByFileAndUser(MessageFile $file, User $user): ?MessageFileUpload {
        return $this->em->getRepository(MessageFileUpload::class)
            ->findOneBy([
                'user' => $user,
                'messageFile' => $file
            ]);
    }

    public function persist(MessageFileUpload $fileUpload): void {
        $this->em->persist($fileUpload);
        $this->em->flush();
    }
}