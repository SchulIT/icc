<?php

namespace App\DataFixtures;

use App\Entity\Grade;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class GradeFixtures extends Fixture {

    /**
     * @return array<string, string> keys: external ID, value: name
     */
    public static function getSekIGradeNames() {
        $grades = [ ];

        $suffix = ['A', 'B', 'C'];
        $suffixCount = count($suffix);

        // Sek I
        for($i = 5; $i <= 10; $i++) {
            for($j = 0; $j < $suffixCount; $j++) {
                $name = sprintf('%d%s', $i, $suffix[$j]);
                $id = str_pad($name, 3, '0', STR_PAD_LEFT);

                $grades[$id] = $name;
            }
        }

        return $grades;
    }

    public static function getSekIIGradeNames() {
        return [ 'EF' => 'EF', 'Q1' => 'Q1', 'Q2' => 'Q2' ];
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        foreach(static::getSekIGradeNames() as $externalId => $name) {
            $manager->persist(
                (new Grade())
                    ->setExternalId($externalId)
                    ->setName($name)
            );
        }

        foreach(static::getSekIIGradeNames() as $name) {
            $manager->persist(
                (new Grade())
                    ->setExternalId($name)
                    ->setName($name)
            );
        }

        $manager->flush();
    }
}