<?php
namespace App\Substitution\Sorting;

use App\Common\Sorting\RoomNameStrategy;
use App\Framework\Sorting\SortingStrategyInterface;
use App\Substitution\Entity\Absence;

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