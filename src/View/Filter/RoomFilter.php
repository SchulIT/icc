<?php

namespace App\View\Filter;

use App\Entity\Room;
use App\Repository\RoomRepositoryInterface;
use App\Sorting\RoomNameStrategy;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

class RoomFilter {
    private $sorter;
    private $roomRepository;

    public function __construct(Sorter $sorter, RoomRepositoryInterface $roomRepository) {
        $this->sorter = $sorter;
        $this->roomRepository = $roomRepository;
    }

    public function handle(?int $roomId) {
        $rooms = ArrayUtils::createArrayWithKeys(
            $this->roomRepository->findAll(),
            function(Room $room) {
                return $room->getId();
            }
        );
        $this->sorter->sort($rooms, RoomNameStrategy::class);

        $room = $roomId !== null ?
            $rooms[$roomId] ?? null : null;

        return new RoomFilterView($rooms, $room);
    }
}