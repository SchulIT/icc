<?php

namespace App\DataFixtures;

use App\Entity\AppointmentCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class AppointmentCategoryFixtures extends Fixture {

    public function __construct(private Generator $generator)
    {
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $categoryNames = [
            'ufrei' => 'Unterrichtsfrei',
            'abi' => 'Abitur',
            'orga' => 'Organisatorisches',
            'konferenzen' => 'Konferenzen',
            'fahrten' => 'Fahren und Exkursionen'
        ];

        $categories = [ ];

        foreach($categoryNames as $externalId => $categoryName) {
            $category = (new AppointmentCategory())
                ->setName($categoryName)
                ->setExternalId($externalId)
                ->setColor($this->generator->hexColor);

            $manager->persist($category);

            $categories[] = $category;
        }

        $manager->flush();
    }
}