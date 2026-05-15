<?php

namespace App\Common\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Common\Entity\Grade;
use App\Common\Entity\GradeLimitedMembership;
use App\Common\Entity\Section;
use App\Common\Entity\Student;
use App\Common\Repository\GradeLimitedMembershipRepositoryInterface;

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