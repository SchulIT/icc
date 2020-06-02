<?php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\RoomTag;
use App\Entity\RoomTagInfo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RoomTagInfoFixtures extends Fixture implements DependentFixtureInterface {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        /** @var RoomTag $wifiTag */
        $wifiTag = $this->getReference(RoomTagFixtures::WifiTagReference);
        /** @var RoomTag $tabletsTag */
        $tabletsTag = $this->getReference(RoomTagFixtures::TabletsTagReference);

        $roomA005 = $manager->getRepository(Room::class)
            ->findOneBy(['name' => 'A005']);

        $tagInfo = (new RoomTagInfo())
            ->setTag($wifiTag)
            ->setRoom($roomA005);

        $manager->persist($tagInfo);

        $roomC205 = $manager->getRepository(Room::class)
            ->findOneBy(['name' => 'C205']);

        $tagInfo = (new RoomTagInfo())
            ->setTag($wifiTag)
            ->setRoom($roomC205);
        $manager->persist($tagInfo);

        $tagInfo= (new RoomTagInfo())
            ->setTag($tabletsTag)
            ->setRoom($roomC205)
            ->setValue(20);
        $manager->persist($tagInfo);

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            RoomFixtures::class,
            RoomTagFixtures::class
        ];
    }
}