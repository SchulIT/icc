<?php

namespace App\Common\Import\Json;

use App\Common\Import\Json\RoomData;
use App\Framework\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RoomsData {

    /**
     * @var RoomData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<' . RoomData::class . '>')]
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