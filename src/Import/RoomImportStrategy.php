<?php

namespace App\Import;

use App\Entity\Room;
use App\Repository\RoomRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\GradesData;
use App\Request\Data\RoomData;
use App\Request\Data\RoomsData;
use App\Utils\ArrayUtils;

class RoomImportStrategy implements ImportStrategyInterface {

    private $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository) {
        $this->roomRepository = $roomRepository;
    }

    /**
     * @param RoomsData $data
     * @return RoomData[]
     */
    public function getData($data): array {
        return $data->getRooms();
    }

    /**
     * @param GradesData $requestData
     * @return Room[]
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->roomRepository->findAll(),
            function (Room $room) {
                return $room->getExternalId();
            });
    }

    /**
     * @param RoomData $data
     * @param GradesData $requestData
     * @return Room
     */
    public function createNewEntity($data, $requestData) {
        $room = (new Room())
            ->setExternalId($data->getId());
        $this->updateEntity($room, $data, $requestData);

        return $room;
    }

    /**
     * @param RoomData $object
     * @param Room[] $existingEntities
     * @return Room|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Room $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Room $entity
     * @param GradesData $requestData
     * @param RoomData $data
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setName($data->getName());
        $entity->setDescription($data->getDescription());
        $entity->setCapacity($data->getCapacity());
    }

    /**
     * @inheritDoc
     */
    public function persist($entity): void {
        $this->roomRepository->persist($entity);
    }

    /**
     * @inheritDoc
     */
    public function remove($entity): void {
        $this->roomRepository->remove($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->roomRepository;
    }
}