<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use App\Entity\Teacher;
use App\Utils\ArrayUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;

class TeacherFixtures extends Fixture {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $acronyms = [ ];
        $size = 100;

        for($i = 0; $i < $size; $i++) {
            $firstname = $this->generator->firstName;
            $lastname = $this->generator->lastName;
            $gender = $this->generator->boolean ?
                Gender::Female() :
                Gender::Male();
            $acronym = $this->generateAcronym($firstname, $lastname);

            if(in_array($acronym, $acronyms)) {
                continue;
            }

            $entity = (new Teacher())
                ->setExternalId($acronym)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setGender($gender)
                ->setAcronym($acronym)
                ->setEmail($this->generateEmail($firstname, $lastname));

            $acronyms[] = $acronym;

            $manager->persist($entity);
        }

        $manager->flush();
    }

    private function generateEmail(string $firstname, string $lastname): string {
        return mb_strtolower(sprintf('%s.%s@school.it', $firstname, $lastname));
    }

    private function generateAcronym(string $firstname, string $lastname) {
        return substr(strtoupper($lastname), 0, 3) . substr(strtoupper($firstname), 0, 1);
    }
}