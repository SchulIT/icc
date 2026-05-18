<?php

namespace App\Common\Import;

use App\Common\Entity\Room;
use App\Common\Repository\ResourceTypeRepositoryInterface;
use App\Book\Repository\RoomRepositoryInterface;
use App\Framework\Import\ImportStrategyInterface;
use App\Framework\Repository\TransactionalRepositoryInterface;
use App\Common\Import\Json\GradesData;
use App\Common\Import\Json\RoomData;
use App\Common\Import\Json\RoomsData;
use App\Framework\Utils\ArrayUtils;

class RoomImportStrategy implements ImportStrategyInterface {

    public function __construct(private RoomRepositoryInterface $roomRepository, private ResourceTypeRepositoryInterface $resourceTypeRepository)
    {
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
            $this->roomRepository->findAllExternal(),
            fn(Room $room) => $room->getExternalId());
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
        $entity->setType($this->resourceTypeRepository->findRoomType());
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
    public function remove($entity, $requestData): bool {
        $this->roomRepository->remove($entity);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->roomRepository;
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Room::class;
    }
}