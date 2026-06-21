<?php

namespace App\Chat\Repository;

use App\Chat\Entity\Chat;
use App\Common\Entity\User;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;

interface ChatRepositoryInterface {

    /**
     * @param PaginationQuery $paginationQuery
     * @param User $user
     * @param bool $archived
     * @return PaginatedResult<Chat>
     */
    public function findAllByUserPaginated(PaginationQuery $paginationQuery, User $user, bool $archived): PaginatedResult;

    public function persist(Chat $chat): void;

    public function remove(Chat $chat): void;

    public function archiveAll(): int;

    public function removeAll(): int;
}
