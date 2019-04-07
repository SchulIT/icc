<?php

namespace App\Import;

class Importer {
    /**
     * @param array $data
     * @param ImportStrategyInterface $strategy
     * @return ImportResult
     */
    public function import($data, ImportStrategyInterface $strategy): ImportResult {
        $repository = $strategy->getRepository();
        $repository->beginTransaction();

        $currentEntities = $strategy->getExistingEntities();
        $updatedEntitiesIds = [ ];

        $addedEntities = [ ];
        $updatedEntities = [ ];
        $removedEntities = [ ];

        // ADD/UPDATE ENTITIES
        foreach ($data as $object) {
            $entity = $strategy->getExistingEntity($object, $currentEntities);

            if ($entity !== null) {
                $updatedEntities[] = $entity;
                $strategy->updateEntity($entity, $object);
                $updatedEntitiesIds[] = $strategy->getEntityId($entity);
            } else {
                $entity = $strategy->createNewEntity($object);
                $addedEntities[] = $entity;
            }

            $strategy->persist($entity);
        }

        // REMOVE ENTITIES
        foreach ($currentEntities as $entity) {
            $id = $strategy->getEntityId($entity);

            if (!in_array($id, $updatedEntitiesIds)) {
                $removedEntities[] = $entity;
                $strategy->remove($entity);
            }
        }

        $repository->commit();

        return new ImportResult($addedEntities, $updatedEntities, $removedEntities);
    }

    /**
     * @param array $data
     * @param RelationsImportStrategyInterface $strategy
     * @return ImportResult
     */
    public function importRelations($data, RelationsImportStrategyInterface $strategy): ImportResult {
        $repository = $strategy->getRepository();
        $repository->beginTransaction();

        $strategy->removeAll();

        $addedEntities = [ ];

        foreach($data as $object) {
            $strategy->persist($object);
            $addedEntities[] = $object;
        }

        $repository->commit();

        return new ImportResult($addedEntities, [], []);
    }
}