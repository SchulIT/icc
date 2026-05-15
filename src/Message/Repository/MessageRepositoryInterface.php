<?php

namespace App\Message\Repository;

use App\Common\Entity\Grade;
use App\Message\Entity\Message;
use App\Message\Entity\MessageFile;
use App\Message\Entity\MessageScope;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface MessageRepositoryInterface {

    /**
     * @param int $id
     * @return Message|null
     */
    public function findOneById(int $id): ?Message;

    /**
     * @param \DateTime|null $today Only return messages which are active on this given date
     * @param StudyGroup[] $studyGroups Only return messages which belong to the given study groups
     * @return Message[]
     */
    public function findBy(MessageScope $scope, UserType $userType, DateTime|null $today = null, array $studyGroups = [ ]): array;

    /**
     * @param MessageScope $scope
     * @param UserType $userType
     * @param \DateTime|null $today Only return messages which are active on this given date
     * @param StudyGroup[] $studyGroups Only return messages which belong to the given study groups
     * @return int
     */
    public function countBy(MessageScope $scope, UserType $userType, DateTime|null $today = null, array $studyGroups = [ ]): int;

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @param MessageScope $scope
     * @param UserType $userType
     * @param DateTime|null $today
     * @param array $studyGroups
     * @param string|null $query
     * @return Paginator
     */
    public function getPaginator(int $itemsPerPage, int &$page, MessageScope $scope, UserType $userType, ?DateTime $today = null, array $studyGroups = [ ], ?string $query = null): Paginator;

    /**
     * @return Message[]
     */
    public function findAllByUserType(UserType $userType, ?User $author = null): array;

    /**
     * @return Message[]
     */
    public function findAllByGrade(Grade $grade, ?User $author = null): array;

    /**
     * @param User $user
     * @return Message[]
     */
    public function findAllByAuthor(User $user): array;

    /**
     * @return Message[]
     */
    public function findAll(): array;

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

    /**
     * @param DateTime $dateTime
     * @return Message[]
     */
    public function findAllNotificationNotSent(DateTime $dateTime): array;

}