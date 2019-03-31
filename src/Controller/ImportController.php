<?php

namespace App\Controller;

use App\Request\Data\AppointmentsData;
use App\Request\Data\ExamsData;
use App\Request\Data\GradesData;
use App\Request\Data\StudentsData;
use App\Request\Data\StudyGroupMembershipsData;
use App\Request\Data\StudyGroupsData;
use App\Request\Data\SubjectsData;
use App\Request\Data\SubstitutionsData;
use App\Request\Data\TeachersData;
use App\Request\Data\TuitionsData;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Response\ImportResponse;
use App\Response\ErrorResponse;

/**
 * @Route("/api/import")
 * @Security("is_granted('ROLE_IMPORT')")
 */
class ImportController extends AbstractController {

    /**
     * Imports appointments. Note: you first must create appointment categories from the web interface.
     *
     * @Route("/appointments", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=AppointmentsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function appointments(AppointmentsData $appointmentsData) {

    }

    /**
     * Imports exams.
     *
     * @Route("/exams", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=ExamsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function exams(ExamsData $examsData) {

    }

    /**
     * Imports grades.
     *
     * @Route("/grades", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=GradesData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function grades(GradesData $gradesData) {

    }

    /**
     * Imports students.
     *
     * @Route("/students", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=StudentsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function students(StudentsData $studentsData) {

    }

    /**
     * Imports study groups.
     *
     * @Route("/studygroups", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=ExamsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function studyGroups(StudyGroupsData $studyGroupsData) {

    }

    /**
     * Imports study group memberships.
     *
     * @Route("/studygroups/memberships", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=StudyGroupMembershipsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function studyGroupsMemberships(StudyGroupMembershipsData $membershipsData) {

    }

    /**
     * Imports subjects.
     *
     * @Route("/subjects", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=SubjectsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function subjects(SubjectsData $subjectsData) {

    }

    /**
     * Imports study groups.
     *
     * @Route("/substitutions", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=SubstitutionsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function substitutions(SubstitutionsData $substitutionsData) {

    }

    /**
     * Imports teachers.
     *
     * @Route("/teachers", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=TeachersData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function teachers(TeachersData $teachersData) {

    }

    /**
     * Imports tuitions.
     *
     * @Route("/tuitions", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=TuitionsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function tuitions(TuitionsData $tuitionsData) {

    }
}