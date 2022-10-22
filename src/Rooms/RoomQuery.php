<?php

namespace App\Rooms;

use App\Entity\RoomTag;

class RoomQuery {
    private array $conditions = [ ];

    public function hasConditions() {
        return count($this->conditions) > 0;
    }

    public function getConditions() {
        return $this->conditions;
    }

    public function addSeats($seats) {
        if(empty($seats) && $seats !== 0) {
            return;
        }

        $seats = intval($seats);

        $this->conditions['seats'] = $seats;
    }

    private function has($key) {
        return isset($this->conditions[$key]);
    }

    private function get($key, $default = null) {
        if(!$this->has($key)) {
            return $default;
        }

        return $this->conditions[$key];
    }

    public function hasSeats() {
        return $this->has('seats');
    }

    public function getSeatsValueOrDefault($default = null) {
        return $this->get('seats', $default);
    }

    public function addTag(RoomTag $tag) {
        $this->conditions[$tag->getId()] = [ ];
    }

    public function addTagWithValue(RoomTag $tag, $value) {
        if(empty($value) && $value !== 0) {
            return;
        }

        $value = intval($value);
        $this->conditions[$tag->getId()] = $value;
    }

    public function hasTag(RoomTag $tag) {
        return $this->has($tag->getId());
    }

    public function getValueOrDefault(RoomTag $tag, $default = null) {
        return $this->get($tag->getId(), $default);
    }
}