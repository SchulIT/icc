<?php

namespace App\DataFixtures;

use App\Entity\TeacherTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class TeacherTagFixtures extends Fixture {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
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