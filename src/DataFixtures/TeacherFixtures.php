<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use App\Entity\Subject;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class TeacherFixtures extends Fixture implements DependentFixtureInterface {

    public function __construct(private Generator $generator)
    {
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $acronyms = [ ];
        $size = 100;
        $allSubjects = $manager->getRepository(Subject::class)->findAll();

        for($i = 0; $i < $size; $i++) {
            $firstname = $this->generator->firstName;
            $lastname = $this->generator->lastName;
            $gender = $this->generator->boolean ?
                Gender::Female :
                Gender::Male;
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

            $subjects = $this->generator->randomElements($allSubjects, $this->generator->numberBetween(2, 3), false);
            foreach($subjects as $subject) {
                $entity->addSubject($subject);
            }

            $acronyms[] = $acronym;

            $manager->persist($entity);
        }

        $manager->flush();
    }

    private function generateEmail(string $firstname, string $lastname): string {
        return mb_strtolower(sprintf('%s.%s@school.it', $firstname, $lastname));
    }

    private function generateAcronym(string $firstname, string $lastname) {
        return mb_substr(mb_strtoupper($lastname), 0, 3) . mb_substr(mb_strtoupper($firstname), 0, 2);
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            SubjectFixtures::class
        ];
    }
}