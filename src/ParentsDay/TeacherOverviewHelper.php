<?php

namespace App\ParentsDay;

use App\Entity\GradeTeacher;
use App\Entity\ParentsDay;
use App\Entity\Student;
use App\Repository\ParentsDayAppointmentRepositoryInterface;
use App\Repository\ParentsDayParentalInformationRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\Sorter;
use App\Sorting\TeacherStrategy;

class TeacherOverviewHelper {

    public function __construct(private readonly SectionResolverInterface $sectionResolver,
                                private readonly ParentsDayAppointmentRepositoryInterface $appointmentRepository,
                                private readonly ParentsDayParentalInformationRepositoryInterface $parentalInformationRepository,
                                private readonly TuitionRepositoryInterface $tuitionRepository,
                                private readonly UserRepositoryInterface $userRepository,
                                private readonly Sorter $sorter) {

    }

    public function collectTeachersForStudent(Student $student, ParentsDay $parentsDay): TeacherOverview {
        $tuitions = $this->tuitionRepository->findAllByStudents([$student], $this->sectionResolver->getCurrentSection());
        $appointments = $this->appointmentRepository->findForStudents([$student], $parentsDay);
        /** @var TeacherItem[] $gradeTeachers */
        $gradeTeachers = $student->getGrade($this->sectionResolver->getCurrentSection())?->getTeachers()?->map(fn(GradeTeacher $teacher) => $teacher->getTeacher())?->toArray() ?? [ ];#

        $teachers = array_merge([], $gradeTeachers); // can arrays be cloned with clone ?!

        $teacherToTuitionsMap = [ ];
        $teacherToAppointmentsMap = [ ];

        $teachersWithRequest = [ ];
        $teachersNotNecessary = [ ];
        $comments = [ ];

        foreach ($tuitions as $tuition) {
            foreach($tuition->getTeachers() as $teacher) {
                $teachers[] = $teacher;

                if(!array_key_exists($teacher->getId(), $teacherToTuitionsMap)) {
                    $teacherToTuitionsMap[$teacher->getId()] = [];
                }

                $teacherToTuitionsMap[$teacher->getId()][] = $tuition;
            }
        }

        foreach($appointments as $appointment) {
            foreach($appointment->getTeachers() as $teacher) {
                if(!array_key_exists($teacher->getId(), $teacherToAppointmentsMap)) {
                    $teacherToAppointmentsMap[$teacher->getId()] = [];
                }

                $teacherToAppointmentsMap[$teacher->getId()][] = $appointment;
            }
        }

        foreach($this->parentalInformationRepository->findForStudent($parentsDay, $student) as $information) {
            if($information->isAppointmentNotNecessary()) {
                $teachersNotNecessary[] = $information->getTeacher();
            }

            if($information->isAppointmentRequested()) {
                $teachersWithRequest[] = $information->getTeacher();
            }

            if(!empty($information->getComment())) {
                if(!isset($comments[$information->getTeacher()->getAcronym()])) {
                    $comments[$information->getTeacher()->getId()] = [ ];
                }

                $comments[$information->getTeacher()->getId()][] = $information->getComment();
            }
        }

        $teachers = array_unique($teachers);
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $items = [ ];

        foreach($teachers as $teacher) {
            $users = $this->userRepository->findAllTeachers([$teacher]);
            $userUuid = null;

            if(count($users) > 0) {
                $userUuid = $users[0]->getUuid();
            }

            $items[] = new TeacherItem(
                $teacher,
                in_array($teacher, $gradeTeachers),
                isset($teacherToAppointmentsMap[$teacher->getId()]),
                in_array($teacher, $teachersWithRequest),
                in_array($teacher, $teachersNotNecessary),
                $comments[$teacher->getId()] ?? [ ],
                $teacherToTuitionsMap[$teacher->getId()] ?? [ ],
                $userUuid,
            );
        }

        return new TeacherOverview($student, $items);
    }
}