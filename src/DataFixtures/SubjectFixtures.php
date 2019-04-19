<?php

namespace App\DataFixtures;

use App\Entity\Subject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SubjectFixtures extends Fixture {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $subjects = [
            'E' => 'Englisch',
            'D' => 'Deutsch',
            'M' => 'Mathematik',
            'F' => 'Französisch',
            'IF' => 'Informatik',
            'L' => 'Latein',
            'MU' => 'Musik',
            'PH' => 'Physik',
            'AG-IF' => 'Informatik AG',
            'AG-MU' => 'Chor'
        ];

        foreach($subjects as $abbreviation => $name) {
            $subject = (new Subject())
                ->setName($name)
                ->setExternalId($abbreviation)
                ->setAbbreviation($abbreviation);

            $manager->persist($subject);
        }

        $manager->flush();
    }
}