<?php

namespace App\Controller;

use App\Dashboard\Absence\AbsenceResolver;
use App\Entity\LessonAttendance;
use App\Entity\LessonEntry;
use App\Entity\Student as StudentEntity;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Response\Api\V1\Student;
use App\Response\Api\V1\Teacher;
use App\Response\Api\V1\Tuition as TuitionResponse;
use App\Section\SectionResolverInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book/xhr")
 */
class BookXhrController extends AbstractController {

    use DateRequestTrait;

    private function returnJson($data, SerializerInterface $serializer): Response {
        $json = $serializer->serialize($data, 'json');
        return new Response($json, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * @Route("/teachers", name="xhr_teachers")
     */
    public function teachers(TeacherRepositoryInterface $teacherRepository, SerializerInterface $serializer) {
        $teachers = [];

        foreach($teacherRepository->findAll() as $teacher) {
            $teachers[] = Teacher::fromEntity($teacher);
        }

        return $this->returnJson($teachers, $serializer);
    }

    /**
     * @Route("/tuition/{uuid}", name="xhr_tuition")
     */
    public function tuition(Tuition $tuition, SerializerInterface $serializer) {
        return $this->returnJson(TuitionResponse::fromEntity($tuition), $serializer);
    }

    /**
     * @Route("/tuition/{uuid}/students", name="xhr_tuition_students")
     */
    public function possiblyAbsentStudents(Tuition $tuition, Request $request, AbsenceResolver $absenceResolver,
                                           LessonAttendanceRepositoryInterface $attendanceRepository, SerializerInterface $serializer) {
        $date = $this->getDateFromRequest($request, 'date');

        if($date === null) {
            throw new BadRequestHttpException('date must not be null.');
        }

        $lesson = $request->query->getInt('lesson', 0);

        if($lesson <= 0) {
            throw new BadRequestHttpException('lesson must be greater than 0');
        }

        $students = [ ];

        /** @var StudyGroupMembership $membership */
        foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
            $students[] = $membership->getStudent();
        }

        $absences = [ ];

        foreach($absenceResolver->resolve($date, $lesson, $students) as $absentStudent) {
            $absences[] = [
                'student' => Student::fromEntity($absentStudent->getStudent()),
                'reason' => $absentStudent->getReason()->getValue()
            ];
        }

        foreach($attendanceRepository->findAbsentByStudents($students, $date) as $attendance) {
            if($attendance->getEntry()->getLessonEnd() < $lesson) {
                $absences[] = [
                    'student' => Student::fromEntity($attendance->getStudent()),
                    'reason' => 'absent_before'
                ];
            }
        }

        $response = [
            'students' => array_map(function(StudentEntity $student) {
                return Student::fromEntity($student);
            }, $students),
            'absent' => $absences
        ];

        return $this->returnJson($response, $serializer);
    }

    /**
     * @Route("/attendances/{uuid}", name="xhr_entry_attendances")
     */
    public function attendances(LessonEntry $entry, SerializerInterface $serializer) {
        $data = [ ];

        /** @var LessonAttendance $attendance */
        foreach($entry->getAttendances() as $attendance) {
            $data[] = [
                'student' => Student::fromEntity($attendance->getStudent()),
                'type' => $attendance->getType()
            ];
        }

        return $this->returnJson($data, $serializer);
    }

    /**
     * @Route("/students", name="xhr_students")
     */
    public function students(StudentRepositoryInterface $studentRepository, SectionResolverInterface $sectionResolver, SerializerInterface $serializer) {
        $students = [ ];
        $section = $sectionResolver->getCurrentSection();

        foreach($studentRepository->findAllBySection($section) as $studentEntity) {
            $students[] = Student::fromEntity($studentEntity, $section);
        }

        return $this->returnJson($students, $serializer);
    }
}