<?php

namespace App\Framework\Utils;

use Closure;
use Doctrine\Common\Collections\Collection;

class CollectionUtils {

    /**
     * @param Collection<T> $collection
     * @param T[] $addEntities
     * @param Closure(T): string $idSelector
     * @return void
     * @template T
     */
    public static function add(Collection $collection, array $addEntities, Closure $idSelector): void {
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
     * Synchronizes a Collection with $targetEntities based on the given $idSelector.
     * @param Collection<T> $collection
     * @param T[] $targetEntities
     * @param Closure(T): (string|int) $idSelector
     * @param Closure(T, T): mixed|null $updateFunc
     * @template T
     */
    public static function synchronize(Collection $collection, array $targetEntities, Closure $idSelector, Closure|null $updateFunc = null): void {
        $currentCollection = clone $collection;
        $currentIds = array_map($idSelector, $collection->toArray());

        $targetEntitiesIds = [ ];
        $targetEntitiesById = [ ];

        foreach($targetEntities as $entity) {
            $entityId = $idSelector($entity);
            $targetEntitiesIds[] = $entityId;
            $targetEntitiesById[$entityId ?: ''] = $entity;

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
