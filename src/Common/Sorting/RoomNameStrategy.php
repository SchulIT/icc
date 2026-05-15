<?php

namespace App\Common\Sorting;

use App\Common\Entity\Room;
use App\Framework\Sorting\AbstractStringPropertyStrategy;

class RoomNameStrategy extends AbstractStringPropertyStrategy {

    /**
     * @param Room $object
     */
    protected function getValue($object): string {
        return $object->getName();
    }
}