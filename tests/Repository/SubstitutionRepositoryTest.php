<?php

namespace App\Tests\Repository;

use App\Entity\Gender;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Repository\SubstitutionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubstitutionRepositoryTest extends KernelTestCase {

    private $textsDoe = [
        'DOE hands exercises to MUE',
        'DOE: hands exercises to MUE',
        'See DOE OneDrive'
    ];

    private $textsNoDoe = [
        'DOEexercises to MUE',
        'MUE hands exercises to MUE',
        'Does this help?'
    ];

    /** @var EntityManagerInterface */
    private $em;

    /** @var Teacher */
    private $teacher;

    /** @var Substitution[] */
    private $substitutions = [ ];

    public function setUp(): void {
        $kernel = static::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->teacher = (new Teacher())
            ->setExternalId('A')
            ->setGender(Gender::X())
            ->setEmail('a@example.com')
            ->setFirstname('Helen')
            ->setLastname('Doe')
            ->setAcronym('DOE');

        $this->em->persist($this->teacher);
        $this->em->flush();

        $texts = array_merge($this->textsDoe, $this->textsNoDoe);

        foreach($texts as $text) {
            $substitution = (new Substitution())
                ->setDate(new DateTime('2020-10-01'))
                ->setExternalId(Uuid::uuid4())
                ->setLessonStart(1)
                ->setLessonEnd(2)
                ->setRemark($text);
            $this->substitutions[] = $substitution;

            $this->em->persist($substitution);
            $this->em->flush();
        }
    }

    public function testResolveTeacherSubstitutionsByAcronym() {
        $repository = new SubstitutionRepository($this->em);
        $substitutions = $repository->findAllForTeacher($this->teacher);

        $this->assertEquals(count($this->textsDoe), count($substitutions));

        foreach($substitutions as $substitution) {
            $this->assertTrue(in_array($substitution->getRemark(), $this->textsDoe), sprintf('Check if "%s" is a valid substitution remark for DOE.', $substitution->getRemark()));
        }
    }

    public function tearDown(): void {
        foreach($this->substitutions as $substitution) {
            $this->em->remove($substitution);
        }

        $this->em->remove($this->teacher);
        $this->em->flush();

        $this->em->close();
        $this->em = null;

        parent::tearDown();
    }
}