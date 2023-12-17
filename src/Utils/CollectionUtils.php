<?php

namespace App\Utils;

use Closure;
use Doctrine\Common\Collections\Collection;

class CollectionUtils {

    public static function add(Collection $collection, array $addEntities, Closure $idSelector) {
        $currentIds = array_map($idSelector, $collection->toArray());
        $addedIds = [ ];

        foreach($addEntities as $entity) {
            $entityId = $idSelector($entity);

            if(!in_array($entityId, $currentIds) && !in_array($entityId, $addedIds)) {
                $addedIds[] = $entityId;
                $collection->add($entity);
            }
        }
    }

    /**
     * Synchronises a Collection with $targetEntities based on the given $idSelector.
     */
    public static function synchronize(Collection $collection, array $targetEntities, Closure $idSelector, Closure|null $updateFunc = null) {
        $currentCollection = clone $collection;
        $currentIds = array_map($idSelector, $collection->toArray());

        $targetEntitiesIds = [ ];
        $targetEntitiesById = [ ];

        foreach($targetEntities as $entity) {
            $entityId = $idSelector($entity);
            $targetEntitiesIds[] = $entityId;
            $targetEntitiesById[$entityId] = $entity;

            if(!in_array($entityId, $currentIds)) {
                $collection->add($entity);
            }
        }

        foreach($currentCollection as $item) {
            $itemId = $idSelector($item);

            if(!in_array($itemId, $targetEntitiesIds)) {
                $collection->removeElement($item);
            } else if($updateFunc !== null) {
                $updateFunc($item, $targetEntitiesById[$itemId]);
            }
        }
    }
}