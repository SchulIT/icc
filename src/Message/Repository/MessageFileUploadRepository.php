<?php

namespace App\Message\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Message\Entity\Message;
use App\Message\Entity\MessageFile;
use App\Message\Entity\MessageFileUpload;
use App\Common\Entity\User;
use App\Message\Repository\MessageFileUploadRepositoryInterface;

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

    public function remove(MessageFileUpload $fileUpload): void {
        $this->em->remove($fileUpload);
        $this->em->flush();
    }
}