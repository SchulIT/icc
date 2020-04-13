<?php

namespace App\View\Filter;

use App\Entity\Room;
use App\Repository\RoomRepositoryInterface;
use App\Sorting\RoomNameStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

class RoomFilter {
    private $sorter;
    private $roomRepository;

    public function __construct(Sorter $sorter, RoomRepositoryInterface $roomRepository) {
        $this->sorter = $sorter;
        $this->roomRepository = $roomRepository;
    }

    public function handle(?string $roomUuid) {
        $rooms = ArrayUtils::createArrayWithKeys(
            $this->roomRepository->findAll(),
            function(Room $room) {
                return (string)$room->getUuid();
            }
        );
        $this->sorter->sort($rooms, RoomNameStrategy::class, SortDirection::Ascending(), true);

        $room = $roomUuid !== null ?
            $rooms[$roomUuid] ?? null : null;

        return new RoomFilterView($rooms, $room);
    }
}