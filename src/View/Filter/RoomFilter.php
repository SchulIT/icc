<?php

namespace App\View\Filter;

use App\Entity\Room;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\RoomRepositoryInterface;
use App\Sorting\RoomNameStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;

class RoomFilter {
    public function __construct(private Sorter $sorter, private RoomRepositoryInterface $roomRepository)
    {
    }

    public function handle(?string $roomUuid, User $user) {
        $rooms = [ ];

        if(EnumArrayUtils::inArray($user->getUserType(), [ UserType::Student(), UserType::Parent()]) === false) {
            $rooms = ArrayUtils::createArrayWithKeys(
                $this->roomRepository->findAll(),
                fn(Room $room) => (string)$room->getUuid()
            );
        }

        $this->sorter->sort($rooms, RoomNameStrategy::class, SortDirection::Ascending(), true);

        $room = $roomUuid !== null ?
            $rooms[$roomUuid] ?? null : null;

        return new RoomFilterView($rooms, $room);
    }
}