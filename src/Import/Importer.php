<?php

namespace App\Import;

class Importer {
    public function import($data, ImportStrategyInterface $strategy) {
        $repository = $strategy->getRepository();
        $repository->beginTransaction();

        $currentEntities = $strategy->getExistingEntities();
        $updatedEntitiesIds = [ ];

        $addedEntities = [ ];
        $updatedEntities = [ ];
        $removedEntities = [ ];

        // ADD/UPDATE ENTITIES
        foreach($data as $object) {
            $entity = $strategy->getExistingEntity($object, $currentEntities);

            if($entity !== null) {
                $updatedEntities[] = $entity;
                $strategy->updateEntity($entity, $data);
                $updatedEntitiesIds[] = $strategy->getEntityId($entity);
            } else {
                $addedEntities[] = $entity;
                $entity = $strategy->createNewEntity($data);
            }

            $strategy->persist($entity);
        }

        // REMOVE ENTITIES
        foreach ($currentEntities as $entity) {
            $id = $strategy->getEntityId($entity);

            if(!in_array($id, $updatedEntitiesIds)) {
                $removedEntities[] = $entity;
                $strategy->remove($entity);
            }
        }

        $repository->commit();
    }
}