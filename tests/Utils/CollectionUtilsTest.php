<?php

namespace App\Tests\Utils;

use App\Utils\CollectionUtils;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class CollectionUtilsTest extends TestCase {
    public function getIdSelector() {
        return function(Entity $entity) {
            return $entity->getId();
        };
    }

    public function testSynchronizeEmptyCollection() {
        $collection = new ArrayCollection();
        $targetCollection = [
            new Entity(1),
            new Entity(2),
            new Entity(3)
        ];

        $this->assertEquals(0, $collection->count());

        CollectionUtils::synchronize($collection, $targetCollection, $this->getIdSelector());
        $this->assertEquals(3, $collection->count());
    }

    public function testSynchronizeWithEmptyCollection() {
        $collection = new ArrayCollection();
        $collection->add(new Entity(1));
        $collection->add(new Entity(2));
        $collection->add(new Entity(3));
        $targetCollection = [ ];

        $this->assertEquals(3, $collection->count());

        CollectionUtils::synchronize($collection, $targetCollection, $this->getIdSelector());

        $this->assertEquals(0, $collection->count());
    }

    public function testSynchronize() {
        $collection = new ArrayCollection();
        $collection->add(new Entity(1));
        $collection->add(new Entity(2));
        $collection->add(new Entity(3));

        $targetCollection = [
            new Entity(1),
            new Entity(3),
            new Entity(5),
            new Entity(7)
        ];

        $this->assertEquals(3, $collection->count());

        CollectionUtils::synchronize($collection, $targetCollection, $this->getIdSelector());

        $this->assertEquals(4, $collection->count());

        $ids = array_map($this->getIdSelector(), $collection->toArray());

        $this->assertSame([1,3,5,7], array_values($ids)); // Important: ignore keys -> array_values($ids);
    }
}

class Entity {
    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    public function getId(): int {
        return $this->id;
    }
}