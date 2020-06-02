<?php

namespace App\View\Filter;

use App\Entity\Room;
use App\Repository\RoomRepositoryInterface;
use App\Sorting\RoomNameStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;

class RoomsFilter {
    private $sorter;
    private $roomRepository;

    public function __construct(Sorter $sorter, RoomRepositoryInterface $roomRepository) {
        $this->sorter = $sorter;
        $this->roomRepository = $roomRepository;
    }

    public function handle(array $roomUuids) {
        $rooms = ArrayUtils::createArrayWithKeys(
            $this->roomRepository->findAll(),
            function(Room $room) {
                return (string)$room->getUuid();
            }
        );
        $this->sorter->sort($rooms, RoomNameStrategy::class, SortDirection::Ascending(), true);

        $selectedRooms = [ ];

        foreach($rooms as $uuid => $room) {
            if(in_array($uuid, $roomUuids)) {
                $selectedRooms[] = $room;
            }
        }

        return new RoomsFilterView($rooms, $selectedRooms);
    }
}