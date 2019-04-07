<?php

namespace App\Utils;

use Doctrine\Common\Collections\Collection;

class CollectionUtils {

    /**
     * Synchronises a Collection with $targetEntities based on the given $idSelector.
     *
     * @param Collection $collection
     * @param array $targetEntities
     * @param \Closure $idSelector
     */
    public static function synchronize(Collection $collection, array $targetEntities, \Closure $idSelector) {
        $currentCollection = clone $collection;
        $currentIds = array_map($idSelector, $collection->toArray());

        $targetEntitiesIds = [ ];

        foreach($targetEntities as $entity) {
            $entityId = $idSelector($entity);
            $targetEntitiesIds[] = $entityId;

            if(!in_array($entityId, $currentIds)) {
                $collection->add($entity);
            }
        }

        foreach($currentCollection as $item) {
            $itemId = $idSelector($item);

            if(!in_array($itemId, $targetEntitiesIds)) {
                $collection->removeElement($item);
            }
        }
    }
}