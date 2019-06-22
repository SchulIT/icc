<?php

namespace App\Sorting;

use App\Entity\Room;

class RoomNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Room $object
     * @return string
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}