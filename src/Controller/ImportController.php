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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ImportController extends AbstractController {

    /**
     * @param AppointmentsData $appointmentsData
     */
    public function appointments(AppointmentsData $appointmentsData) {

    }

    public function exams(ExamsData $examsData) {

    }

    public function grades(GradesData $gradesData) {

    }

    public function students(StudentsData $studentsData) {

    }

    public function studyGroups(StudyGroupsData $studyGroupsData) {

    }

    public function studyGroupsMemberships(StudyGroupMembershipsData $membershipsData) {

    }

    public function subjects(SubjectsData $subjectsData) {

    }

    public function substitutions(SubstitutionsData $substitutionsData) {

    }

    public function teachers(TeachersData $teachersData) {

    }

    public function tuitions(TuitionsData $tuitionsData) {

    }
}