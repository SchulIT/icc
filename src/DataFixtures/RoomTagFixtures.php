<?php

namespace App\DataFixtures;

use App\Entity\RoomTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoomTagFixtures extends Fixture {

    public const WifiTagReference = 'room-tag-wifi';
    public const TabletsTagReference = 'room-tag-tables';

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $roomTagWifi = (new RoomTag())
            ->setName('WLAN AP')
            ->setDescription('In diesem Raum befindet sich ein WLAN AP.');
        $manager->persist($roomTagWifi);

        $roomTagTablets = (new RoomTag())
            ->setName('Tablets')
            ->setDescription('In diesem Raum befinden sich Tablets')
            ->setHasValue(true);

        $manager->persist($roomTagTablets);
        $manager->flush();

        $this->addReference(static::WifiTagReference, $roomTagWifi);
        $this->addReference(static::TabletsTagReference, $roomTagTablets);
    }
}