<?php

namespace App\ParentsDay;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayParentalInformation;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Repository\ParentsDayParentalInformationRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\ParentsDayParentalInformationStrategy;
use App\Sorting\Sorter;

class ParentsDayParentalInformationResolver {
    public function __construct(private readonly ParentsDayParentalInformationRepositoryInterface $repository,
                                private readonly SectionResolverInterface $sectionResolver,
                                private readonly TuitionRepositoryInterface $tuitionRepository,
                                private readonly Sorter $sorter) {

    }

    public function findOrCreateForStudent(ParentsDay $parentsDay, Student $student): array {
        $tuitions = $this->tuitionRepository->findAllByStudents([$student], $this->sectionResolver->getSectionForDate($parentsDay->getDate()));
        $teachers = [ ];

        $information = $this->repository->findForStudent($parentsDay, $student);

        foreach($information as $singleInformation) {
            $teachers[] = $singleInformation->getTeacher()->getId();
        }

        foreach($tuitions as $tuition) {
            foreach($tuition->getTeachers() as $teacher) {
                if(!in_array($teacher->getId(), $teachers)) {
                    $information[] = (new ParentsDayParentalInformation())->setParentsDay($parentsDay)->setStudent($student)->setTeacher($teacher);
                }
            }
        }

        $this->sorter->sort($information, ParentsDayParentalInformationStrategy::class);

        return $information;
    }

    public function findOrCreateForTeacherAndTuition(ParentsDay $parentsDay, Teacher $teacher, Tuition $tuition): array {
        $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();

        $alreadyPresent = [ ];
        $information = $this->repository->findForTeacherAndStudents($parentsDay, $teacher, $students);

        foreach($information as $singleInformation) {
            $alreadyPresent[] = $singleInformation->getStudent()->getId();
        }

        foreach($students as $student) {
            if(!in_array($student->getId(), $alreadyPresent)) {
                $information[] = (new ParentsDayParentalInformation())->setParentsDay($parentsDay)->setStudent($student)->setTeacher($teacher);
            }
        }

        $this->sorter->sort($information, ParentsDayParentalInformationStrategy::class);

        return $information;
    }
}