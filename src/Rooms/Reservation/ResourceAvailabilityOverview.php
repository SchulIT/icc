<?php

namespace App\Rooms\Reservation;

use App\Entity\Resource;

class ResourceAvailabilityOverview {
    private $maxLessons;

    private $resources = [ ];

    public function __construct(int $maxLessons) {
        $this->maxLessons = $maxLessons;
    }

    /**
     * @return int
     */
    public function getMaxLessons(): int {
        return $this->maxLessons;
    }

    public function addAvailability(Resource $resource, int $lessonNumber, ResourceAvailability $availability) {
        if(!isset($this->resources[$resource->getId()])) {
            $this->resources[$resource->getId()] = [ ];
        }

        $this->resources[$resource->getId()][$lessonNumber] = $availability;
    }

    public function getAvailability(Resource $resource, int $lessonNumber): ?ResourceAvailability {
        return $this->resources[$resource->getId()][$lessonNumber] ?? null;
    }
}