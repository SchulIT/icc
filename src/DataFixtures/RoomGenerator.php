<?php

namespace App\DataFixtures;

use Faker\Generator;

class RoomGenerator {

    private $generator;

    private $rooms = [ ];

    public function __construct(Generator $generator) {
        $this->generator = $generator;
        $this->loadRooms();
    }

    private function loadRooms(): void {
        foreach(['A', 'B', 'C'] as $building) {
            for($floor = 0; $floor <= 3; $floor++) {
                for($room = 0; $room <= 10; $room++) {
                    $this->rooms[] = sprintf(
                        '%s%s',
                        $building,
                        str_pad((string)($floor * 100 + $room), 3, '0', STR_PAD_LEFT)
                    );
                }
            }
        }
    }

    public function getRoom(): string {
        return $this->generator->randomElement($this->rooms);
    }

    public function getRooms(int $count): array {
        return $this->generator->randomElements($this->rooms, $count);
    }
}