<?php

namespace App\ParentsDay;

use App\Common\Entity\GradeTeacher;
use App\ParentsDay\Entity\ParentsDay;
use App\ParentsDay\Entity\ParentsDayTeacherRoom;
use App\Common\Entity\Student;
use App\ParentsDay\Repository\ParentsDayAppointmentRepositoryInterface;
use App\ParentsDay\Repository\ParentsDayParentalInformationRepositoryInterface;
use App\ParentsDay\Repository\ParentsDayTeacherRoomRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Common\Repository\UserRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\TeacherStrategy;

class TeacherOverviewHelper {

    public function __construct(private readonly SectionResolverInterface $sectionResolver,
                                private readonly ParentsDayAppointmentRepositoryInterface $appointmentRepository,
                                private readonly ParentsDayParentalInformationRepositoryInterface $parentalInformationRepository,
                                private readonly ParentsDayTeacherRoomRepositoryInterface $teacherRoomRepository,
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
                $this->teacherRoomRepository->findRoomByTeacherAndParentsDay($teacher, $parentsDay)
            );
        }

        return new TeacherOverview($student, $items);
    }
}