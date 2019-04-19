<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use App\Entity\Teacher;
use App\Utils\ArrayUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TeacherFixtures extends Fixture {

    // Source: https://uinames.com/api/?amount=60&region=united+states
    private $teachers = <<<JSON
[{"name":"Nancy","surname":"Green","gender":"female","region":"United States"},{"name":"Alexander","surname":"Tran","gender":"male","region":"United States"},{"name":"Randy","surname":"Moreno","gender":"male","region":"United States"},{"name":"Craig","surname":"Reyes","gender":"male","region":"United States"},{"name":"Juan","surname":"Willis","gender":"male","region":"United States"},{"name":"Joshua","surname":"Barrett","gender":"male","region":"United States"},{"name":"Marie","surname":"Ross","gender":"female","region":"United States"},{"name":"Donna","surname":"Collins","gender":"female","region":"United States"},{"name":"Dylan","surname":"Schultz","gender":"male","region":"United States"},{"name":"Judy","surname":"Welch","gender":"female","region":"United States"},{"name":"Ashley","surname":"Flores","gender":"female","region":"United States"},{"name":"Rebecca","surname":"Reynolds","gender":"female","region":"United States"},{"name":"Brittany","surname":"Wilson","gender":"female","region":"United States"},{"name":"Peter","surname":"Pena","gender":"male","region":"United States"},{"name":"Alan","surname":"Hayes","gender":"male","region":"United States"},{"name":"Jesse","surname":"Knight","gender":"male","region":"United States"},{"name":"Marilyn","surname":"Morris","gender":"female","region":"United States"},{"name":"Russell","surname":"Stephens","gender":"male","region":"United States"},{"name":"Joseph","surname":"Williams","gender":"male","region":"United States"},{"name":"Hannah","surname":"Tucker","gender":"female","region":"United States"},{"name":"Hannah","surname":"Little","gender":"female","region":"United States"},{"name":"Vincent","surname":"Spencer","gender":"male","region":"United States"},{"name":"Diana","surname":"Morales","gender":"female","region":"United States"},{"name":"Kimberly","surname":"Banks","gender":"female","region":"United States"},{"name":"Alice","surname":"Wheeler","gender":"female","region":"United States"},{"name":"Laura","surname":"Grant","gender":"female","region":"United States"},{"name":"Susan","surname":"Bowman","gender":"female","region":"United States"},{"name":"John","surname":"Edwards","gender":"male","region":"United States"},{"name":"Bruce","surname":"Contreras","gender":"male","region":"United States"},{"name":"Mildred","surname":"Jackson","gender":"female","region":"United States"},{"name":"Rebecca","surname":"Salazar","gender":"female","region":"United States"},{"name":"Amber","surname":"Gibson","gender":"female","region":"United States"},{"name":"Kelly","surname":"Anderson","gender":"female","region":"United States"},{"name":"Adam","surname":"Berry","gender":"male","region":"United States"},{"name":"Philip","surname":"Carr","gender":"male","region":"United States"},{"name":"Jesse","surname":"Garrett","gender":"male","region":"United States"},{"name":"Timothy","surname":"Gonzales","gender":"male","region":"United States"},{"name":"Ryan","surname":"West","gender":"male","region":"United States"},{"name":"Joe","surname":"Day","gender":"male","region":"United States"},{"name":"Grace","surname":"Franklin","gender":"female","region":"United States"},{"name":"Harry","surname":"Cole","gender":"male","region":"United States"},{"name":"George","surname":"Riley","gender":"male","region":"United States"},{"name":"Olivia","surname":"Anderson","gender":"female","region":"United States"},{"name":"Raymond","surname":"Bryant","gender":"male","region":"United States"},{"name":"James","surname":"Alvarez","gender":"male","region":"United States"},{"name":"Jordan","surname":"Gonzalez","gender":"male","region":"United States"},{"name":"Roy","surname":"Burns","gender":"male","region":"United States"},{"name":"Lauren","surname":"Jordan","gender":"female","region":"United States"},{"name":"Martha","surname":"Brooks","gender":"female","region":"United States"},{"name":"Craig","surname":"McCoy","gender":"male","region":"United States"},{"name":"Philip","surname":"Schultz","gender":"male","region":"United States"},{"name":"Nicholas","surname":"Stevens","gender":"male","region":"United States"},{"name":"Jacqueline","surname":"Walters","gender":"female","region":"United States"},{"name":"Alexander","surname":"Burton","gender":"male","region":"United States"},{"name":"Christian","surname":"Carpenter","gender":"male","region":"United States"},{"name":"Joan","surname":"Schultz","gender":"female","region":"United States"},{"name":"Nancy","surname":"Tran","gender":"female","region":"United States"},{"name":"Michelle","surname":"Weaver","gender":"female","region":"United States"},{"name":"Terry","surname":"Cole","gender":"male","region":"United States"},{"name":"Rebecca","surname":"Smith","gender":"female","region":"United States"}]
JSON;


    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $teachers = json_decode($this->teachers);

        $teachers = ArrayUtils::createArrayWithKeys($teachers, function($teacher) {
            return $this->generateAcronym($teacher->surname);
        });

        foreach($teachers as $acronym => $teacher) {
            $entity = (new Teacher())
                ->setExternalId($acronym)
                ->setFirstname($teacher->name)
                ->setLastname($teacher->surname)
                ->setGender(new Gender($teacher->gender))
                ->setAcronym($acronym)
                ->setEmail($this->generateEmail($teacher->name, $teacher->surname));

            $manager->persist($entity);
        }

        $manager->flush();
    }

    private function generateEmail(string $firstname, string $lastname): string {
        return mb_strtolower(sprintf('%s.%s@school.it', $firstname, $lastname));
    }

    private function generateAcronym(string $lastname) {
        return substr(strtoupper($lastname), 0, 4);
    }
}