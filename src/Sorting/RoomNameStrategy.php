<?php

namespace App\Sorting;

use App\Entity\Room;

class RoomNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Room $object
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}