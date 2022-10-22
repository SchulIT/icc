<?php

namespace App\Rooms\Reservation;

use App\Entity\ResourceEntity;

class ResourceAvailabilityOverview {
    private array $resources = [ ];

    public function __construct(private int $maxLessons)
    {
    }

    public function getMaxLessons(): int {
        return $this->maxLessons;
    }

    public function addAvailability(ResourceEntity $resource, int $lessonNumber, ResourceAvailability $availability) {
        if(!isset($this->resources[$resource->getId()])) {
            $this->resources[$resource->getId()] = [ ];
        }

        $this->resources[$resource->getId()][$lessonNumber] = $availability;
    }

    public function getAvailability(ResourceEntity $resource, int $lessonNumber): ?ResourceAvailability {
        return $this->resources[$resource->getId()][$lessonNumber] ?? null;
    }
}