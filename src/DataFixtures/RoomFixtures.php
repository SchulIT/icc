<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;

class RoomFixtures extends Fixture {

    private $generator;
    private $roomGenerator;

    public function __construct(Generator $generator, RoomGenerator $roomGenerator) {
        $this->generator = $generator;
        $this->roomGenerator = $roomGenerator;
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