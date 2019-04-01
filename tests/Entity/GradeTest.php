<?php

namespace App\Tests\Entity;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudentStatus;
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

        $student = (new Student())
            ->setGender(Gender::X())
            ->setGrade($grade)
            ->setStatus(StudentStatus::Active())
            ->setLastname('lastname')
            ->setFirstname('firstname')
            ->setExternalId('external-id');

        $em->persist($grade);
        $em->persist($student);
        $em->flush();

        $em->clear();

        /** @var Grade $grade */
        $grade = $em->getRepository(Grade::class)
            ->findOneBy([
                'id' => $grade->getId()
            ]);

        $this->assertEquals(1, $grade->getStudents()->count());
        $this->assertEquals($student->getId(), $grade->getStudents()->first()->getId());
    }

}