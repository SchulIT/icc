<?php
namespace App\Sorting;

use App\Entity\Absence;

class AbsentRoomStrategy implements SortingStrategyInterface {

    public function __construct(private readonly RoomNameStrategy $strategy) { }

    /**
     * @param Absence $objectA
     * @param Absence $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return $this->strategy->compare($objectA->getRoom(), $objectB->getRoom());
    }
}