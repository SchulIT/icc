<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RoomsData {

    /**
     * @Serializer\Type("array<App\Request\Data\RoomData>")
     * @Serializer\SerializedName("rooms")
     * @Assert\Valid()
     * @UniqueId(propertyPath="id")
     * @var RoomData[]
     */
    private $rooms = [ ];

    /**
     * @return RoomData[]
     */
    public function getRooms() {
        return $this->rooms;
    }
}