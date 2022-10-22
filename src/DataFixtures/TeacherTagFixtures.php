<?php

namespace App\DataFixtures;

use App\Entity\TeacherTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class TeacherTagFixtures extends Fixture {

    public function __construct(private Generator $generator)
    {
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $tagNames = [
            'leitung' => 'Schulleitung',
            'ref' => 'Referendar',
            'rat' => 'Lehrerrat'
        ];

        foreach($tagNames as $externalId => $name) {
            $tag = (new TeacherTag())
                ->setExternalId($externalId)
                ->setName($name)
                ->setColor($this->generator->hexColor);

            $manager->persist($tag);
        }

        $manager->flush();
    }
}