<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface NotificationRepositoryInterface {

    /**
     * @param User $user
     * @param int $itemsPerPage
     * @param int $page
     * @return Paginator
     */
    public function getUserPaginator(User $user, int $itemsPerPage, int &$page): Paginator;

    /**
     * @param User $user
     * @return Notification[]
     */
    public function findUnreadForUser(User $user): array;

    public function countUnreadForUser(User $user): int;

    public function markAllReadForUser(User $user): int;

    public function persist(Notification $notification): void;

    public function remove(Notification $notification): void;
}