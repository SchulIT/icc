<?php

namespace App\Common\View\Filter;

use App\Common\Entity\Room;
use App\Book\Repository\RoomRepositoryInterface;
use App\Common\Sorting\RoomNameStrategy;
use App\Framework\Sorting\SortDirection;
use App\Framework\Sorting\Sorter;
use App\Framework\Utils\ArrayUtils;
use App\Common\View\Filter\RoomsFilterView;

class RoomsFilter {
    public function __construct(private Sorter $sorter, private RoomRepositoryInterface $roomRepository)
    {
    }

    public function handle(array $roomUuids) {
        $rooms = ArrayUtils::createArrayWithKeys(
            $this->roomRepository->findAll(),
            fn(Room $room) => (string)$room->getUuid()
        );
        $this->sorter->sort($rooms, RoomNameStrategy::class, SortDirection::Ascending, true);

        $selectedRooms = [ ];

        foreach($rooms as $uuid => $room) {
            if(in_array($uuid, $roomUuids)) {
                $selectedRooms[] = $room;
            }
        }

        return new RoomsFilterView($rooms, $selectedRooms);
    }
}