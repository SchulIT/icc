<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RoomsData {

    /**
     * @var RoomData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<App\Request\Data\RoomData>')]
    #[Serializer\SerializedName('rooms')]
    private array $rooms = [ ];

    /**
     * @return RoomData[]
     */
    public function getRooms() {
        return $this->rooms;
    }

    /**
     * @param RoomData[] $rooms
     */
    public function setRooms(array $rooms): RoomsData {
        $this->rooms = $rooms;
        return $this;
    }
}