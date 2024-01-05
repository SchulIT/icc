<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\GradeLimitedMembership;
use App\Entity\Section;
use App\Entity\Student;

class GradeLimitedMembershipRepository extends AbstractRepository implements GradeLimitedMembershipRepositoryInterface {

    public function findAllByGradeAndSection(Grade $grade, Section $section): array {
        return $this->em->getRepository(GradeLimitedMembership::class)
            ->findBy(['section' => $section, 'grade' => $grade]);
    }

    public function findAllByStudentAndSection(Student $student, Section $section): array {
        return $this->em->getRepository(GradeLimitedMembership::class)
            ->findBy(['section' => $section, 'student' => $student]);
    }
}