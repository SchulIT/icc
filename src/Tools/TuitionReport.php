<?php

namespace App\Tools;

use App\Csv\CsvHelper;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\StudyGroupMembership;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Sorting\TeacherStrategy;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TuitionReport {

    public function __construct(private readonly CsvHelper $csvHelper,
                                private readonly DateHelper $dateHelper,
                                private readonly TranslatorInterface $translator,
                                private readonly EntityManagerInterface $em,
                                private readonly Sorter $sorter) {

    }

    public function generateReport(Section $section, array $types): array {
        $types = $this->sanitizeTypes($types);

        $result = [ ];
        $headers =
            [
                $this->translator->trans('label.acronym'),
                $this->translator->trans('label.subject'),
                $this->translator->trans('label.grades'),
                $this->translator->trans('label.name'),
                $this->translator->trans('label.students_simple')
            ];

        /** @var Tuition[] $tuitions */
        $tuitions = $this->em->createQueryBuilder()
            ->select(['t', 's', 'tt', 'sg', 'sgm', 'g'])
            ->from(Tuition::class, 't')
            ->leftJoin('t.subject', 's')
            ->leftJoin('t.teachers', 'tt')
            ->leftJoin('t.studyGroup', 'sg')
            ->leftJoin('sg.grades', 'g')
            ->leftJoin('sg.memberships', 'sgm')
            ->where('t.section = :section')
            ->setParameter('section', $section->getId())
            ->getQuery()
            ->getResult();

        foreach($tuitions as $tuition) {
            $teachers = $tuition->getTeachers()->toArray();
            $this->sorter->sort($teachers, TeacherStrategy::class);

            $grades = $tuition->getStudyGroup()->getGrades()->toArray();
            $this->sorter->sort($grades, GradeNameStrategy::class);

            $row = [
                implode(', ', array_map(fn(Teacher $teacher) => $teacher->getAcronym(), $teachers)),
                $tuition->getSubject()->getAbbreviation(),
                implode(', ', array_map(fn(Grade $grade) => $grade->getName(), $grades)),
                $tuition->getName(),
                $tuition->getStudyGroup()->getMemberships()->count()
            ];

            foreach($types as $type) {
                $row[] = $tuition->getStudyGroup()->getMemberships()->filter(fn(StudyGroupMembership $membership) => $membership->getType() === $type)->count();
            }

            $result[] = $row;
        }

        usort($result, fn(array $rowA, array $rowB) => strnatcasecmp($rowA[0], $rowB[0]));

        // finally add header
        array_unshift($result, array_merge($headers, $types));

        return $result;
    }

    private function sanitizeTypes(array $types): array {
        $result = [ ];
        $validTypes = $this->getMembershipTypes();

        foreach($types as $type) {
            if(in_array($type, $validTypes)) {
                $result[] = $type;
            }
        }

        return $result;
    }

    public function getMembershipTypes() : array {
        return $this->em->createQueryBuilder()
            ->select('DISTINCT(m.type)')
            ->from(StudyGroupMembership::class, 'm')
            ->where('m.type IS NOT NULL')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function generateReportAsCsvResponse(Section $section, array $types): Response {
        return $this->csvHelper->getCsvResponse(
            sprintf(
                '%s-%s-%d-%d.csv',
                $this->dateHelper->getToday()->format('Y-m-d'),
                'tuition-report',
                $section->getYear(),
                $section->getNumber()
            ),
            $this->generateReport($section, $types)
        );
    }
}