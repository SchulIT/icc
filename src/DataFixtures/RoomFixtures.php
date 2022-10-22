<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class RoomFixtures extends Fixture {

    public function __construct(private Generator $generator, private RoomGenerator $roomGenerator)
    {
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        foreach($this->roomGenerator->getRooms() as $roomName) {
            $room = (new Room())
                ->setName($roomName)
                ->setCapacity($this->generator->numberBetween(25, 35));

            $manager->persist($room);
        }

        $manager->flush();
    }
}