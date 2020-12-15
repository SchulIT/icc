<?php

namespace App\Tests\Import;

use App\Entity\Room;
use App\Import\Importer;
use App\Import\RoomImportStrategy;
use App\Repository\ImportDateTypeRepository;
use App\Repository\RoomRepository;
use App\Repository\RoomTagRepository;
use App\Request\Data\RoomData;
use App\Request\Data\RoomsData;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoomImportStrategyTest extends WebTestCase {
    private $em;
    private $validator;

    public function setUp(): void {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->validator = $kernel
            ->getContainer()
            ->get('validator');

        $this->em = $kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $roomOne = (new Room())
            ->setExternalId('ROOM1')
            ->setName('ROOM1');

        $roomTwo = (new Room())
            ->setExternalId(null)
            ->setName('ROOM2');

        $roomFour = (new Room())
            ->setExternalId('ROOM4')
            ->setName('ROOM4');

        $this->em->persist($roomOne);
        $this->em->persist($roomTwo);
        $this->em->persist($roomFour);
        $this->em->flush();
    }

    public function testImportRooms() {
        $data = (new RoomsData())
            ->setRooms([
                (new RoomData())
                    ->setId('ROOM1')
                    ->setName('Raum 1')
                    ->setCapacity(20)
                    ->setDescription('Raum 1 Beschreibung'),
                (new RoomData())
                    ->setId('ROOM3')
                    ->setName('Raum 3')
                    ->setCapacity(10)
                    ->setDescription('Raum 3 Beschreibung'),
            ]);

        $tagRepository = new RoomTagRepository($this->em);
        $repository = new RoomRepository($this->em, $tagRepository);
        $dateTimeRepository = new ImportDateTypeRepository($this->em);
        $importer = new Importer($this->validator, $dateTimeRepository, new NullLogger());
        $strategy = new RoomImportStrategy($repository);
        $result = $importer->import($data, $strategy);

        /** @var Room[] $addedRooms */
        $addedRooms = $result->getAdded();
        $this->assertEquals(1, count($addedRooms));
        $this->assertEquals('ROOM3', $addedRooms[0]->getExternalId());
        $this->assertEquals('Raum 3', $addedRooms[0]->getName());
        $this->assertEquals(10, $addedRooms[0]->getCapacity());
        $this->assertEquals('Raum 3 Beschreibung', $addedRooms[0]->getDescription());

        /** @var Room[] $updatedRooms */
        $updatedRooms = $result->getUpdated();
        $this->assertEquals(1, count($updatedRooms));
        $this->assertEquals('ROOM1', $updatedRooms[0]->getExternalId());
        $this->assertEquals('Raum 1', $updatedRooms[0]->getName());
        $this->assertEquals(20, $updatedRooms[0]->getCapacity());
        $this->assertEquals('Raum 1 Beschreibung', $updatedRooms[0]->getDescription());

        /** @var Room[] $removedRooms */
        $removedRooms = $result->getRemoved();
        $this->assertEquals(1, count($removedRooms));
        $this->assertEquals('ROOM4', $removedRooms[0]->getExternalId());
    }
}