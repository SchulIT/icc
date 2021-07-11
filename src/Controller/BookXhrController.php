<?php

namespace App\Controller;

use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\TeacherRepositoryInterface;
use App\Response\Api\V1\Student;
use App\Response\Api\V1\Teacher;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Response\Api\V1\Tuition as TuitionResponse;

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
    public function possiblyAbsentStudents(Tuition $tuition, Request $request, SerializerInterface $serializer) {
        $date = $this->getDateFromRequest($request, 'date');

        if($date === null) {
            throw new BadRequestHttpException('date must not be null.');
        }

        $students = [ ];

        /** @var StudyGroupMembership $membership */
        foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
            $students[] = Student::fromEntity($membership->getStudent());
        }

        return $this->returnJson($students, $serializer);
    }
}