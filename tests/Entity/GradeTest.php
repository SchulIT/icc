<?php

namespace App\Tests\Entity;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\Section;
use App\Entity\Student;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GradeTest extends WebTestCase {
    public function testGettersSetters() {
        $grade = new Grade();

        $this->assertNull($grade->getId());

        $grade->setName('name');
        $this->assertEquals('name', $grade->getName());
    }

    public function testGetStudents() {
        $kernel = static::createKernel();
        $kernel->boot();

        $em = $kernel->getContainer()->get('doctrine')
            ->getManager();

        $grade = (new Grade())
            ->setName('grade');

        $section = (new Section())
            ->setYear(2020)
            ->setNumber(1)
            ->setDisplayName('Testabschnitt')
            ->setStart(new DateTime('2020-08-16'))
            ->setEnd(new DateTime('2021-01-31'));

        $student = (new Student())
            ->setUniqueIdentifier(md5(uniqid()))
            ->setGender(Gender::X())
            ->setStatus('active')
            ->setBirthday((new DateTime())->modify('-10 year'))
            ->setLastname('lastname')
            ->setFirstname('firstname')
            ->setExternalId('external-id');

        $student->addGradeMembership(
            (new GradeMembership())
                ->setGrade($grade)
                ->setStudent($student)
                ->setSection($section)
        );

        $em->persist($section);
        $em->persist($grade);
        $em->persist($student);
        $em->flush();

        $em->clear();

        /** @var Grade $grade */
        $grade = $em->getRepository(Grade::class)
            ->findOneBy([
                'id' => $grade->getId()
            ]);

        $this->assertEquals(1, $grade->getMemberships()->count());
        $this->assertEquals($student->getId(), $grade->getMemberships()->first()->getId());
    }

}