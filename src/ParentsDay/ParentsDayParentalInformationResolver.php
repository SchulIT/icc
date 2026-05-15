<?php

namespace App\ParentsDay;

use App\ParentsDay\Entity\ParentsDay;
use App\ParentsDay\Entity\ParentsDayParentalInformation;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Entity\Teacher;
use App\Common\Entity\Tuition;
use App\ParentsDay\Repository\ParentsDayParentalInformationRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\ParentsDay\Sorting\ParentsDayParentalInformationStrategy;
use App\Framework\Sorting\Sorter;

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