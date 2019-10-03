<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\MessageFile;
use App\Entity\MessageScope;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;

interface MessageRepositoryInterface {

    /**
     * @param int $id
     * @return Message|null
     */
    public function findOneById(int $id): ?Message;

    /**
     * @param MessageScope $scope
     * @param UserType $userType
     * @param \DateTime|null $today Only return messages which are active on this given date
     * @param StudyGroup[] $studyGroups Only return messages which belong to the given study groups
     * @param bool $archive
     * @return Message[]
     */
    public function findBy(MessageScope $scope, UserType $userType, \DateTime $today = null, array $studyGroups = [ ], bool $archive);

    /**
     * @param UserType $userType
     * @return Message[]
     */
    public function findAllByUserType(UserType $userType);

    /**
     * @return Message[]
     */
    public function findAll();

    /**
     * @param Message $message
     */
    public function persist(Message $message): void;

    /**
     * @param Message $message
     */
    public function remove(Message $message): void;

    /**
     * @param MessageFile $file
     */
    public function removeMessageFile(MessageFile $file): void;

}