<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Room;
use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Book\Repository\RoomRepositoryInterface;
use App\Common\Sorting\RoomNameStrategy;
use App\Framework\Sorting\SortDirection;
use App\Framework\Sorting\Sorter;
use App\Framework\Utils\ArrayUtils;
use App\Framework\Utils\EnumArrayUtils;
use App\Common\View\Filter\RoomFilterView;

class RoomFilter {
    public function __construct(private Sorter $sorter, private RoomRepositoryInterface $roomRepository)
    {
    }

    public function handle(?string $roomUuid, User $user) {
        $rooms = [ ];

        if($user->isStudentOrParent() === false) {
            $rooms = ArrayUtils::createArrayWithKeys(
                $this->roomRepository->findAll(),
                fn(Room $room) => (string)$room->getUuid()
            );
        }

        $this->sorter->sort($rooms, RoomNameStrategy::class, SortDirection::Ascending, true);

        $room = $roomUuid !== null ?
            $rooms[$roomUuid] ?? null : null;

        return new RoomFilterView($rooms, $room);
    }
}