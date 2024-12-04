<?php

namespace App\Tools\SubstitutionEvaluation;

use App\Entity\Substitution;
use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class ReportManager {

    public function __construct(private readonly EntityManagerInterface $entityManager) {

    }

    /**
     * @return string[]
     */
    public function getSubstitutionTypes(): array {
        return $this->entityManager->createQueryBuilder()
            ->select('DISTINCT s.type')
            ->from(Substitution::class, 's')
            ->orderBy('s.type', 'asc')
            ->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * @param ReportInput $input
     * @return TeacherRow[]
     */
    public function evaluate(ReportInput $input): array {
        /** @var Substitution[] $substitutions */
        $substitutions = $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(Substitution::class, 's')
            ->where('s.type IN (:types)')
            ->andWhere('s.date >= :start')
            ->andWhere('s.date <= :end')
            ->setParameter('types', $input->substitutionTypes)
            ->setParameter('start', $input->start)
            ->setParameter('end', $input->end)
            ->getQuery()
            ->getResult();

        $result = [ ];

        foreach($substitutions as $substitution) {
            $numLessons = $substitution->getLessonEnd() - $substitution->getLessonStart() + 1;

            // Test if teacher(s) are same -> count nothing
            $teacherIds = $substitution->getTeachers()->map(fn(Teacher $teacher) => $teacher->getId())->toArray();
            $replacementTeacherIds = $substitution->getReplacementTeachers()->map(fn(Teacher $teacher) => $teacher->getId())->toArray();

            if($teacherIds == $replacementTeacherIds) {
                continue; // teachers are same -> ignore
            }

            foreach($substitution->getTeachers() as $teacher) {
                if(!isset($result[$teacher->getId()])) {
                    $result[$teacher->getId()] = new TeacherRow($teacher);
                }

                $result[$teacher->getId()]->numWasSubstituted += $numLessons;
            }

            foreach($substitution->getReplacementTeachers() as $teacher) {
                if(!isset($result[$teacher->getId()])) {
                    $result[$teacher->getId()] = new TeacherRow($teacher);
                }

                $result[$teacher->getId()]->numSubstitute += $numLessons;
            }
        }

        return array_values($result);
    }
}