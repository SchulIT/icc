<?php

namespace App\Controller;

use App\Entity\StudyGroup;
use App\Grouping\Grouper;
use App\Grouping\StudentGradeGroup;
use App\Grouping\StudentGradeStrategy;
use App\Repository\GradeRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Sorting\GradeStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentStrategy;
use App\Utils\ArrayUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListsController extends AbstractController {

    private $grouper;
    private $sorter;

    public function __construct(Grouper $grouper, Sorter $sorter) {
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("/lists/study_groups", name="lists_studygroups")
     */
    public function studyGroups(GradeRepositoryInterface $gradeRepository, StudentRepositoryInterface $studentRepository,
                                StudyGroupRepositoryInterface $studyGroupRepository) {
        $grades = $gradeRepository->findAll();
        $this->sorter->sort($grades, GradeStrategy::class);

        /** @var StudentGradeGroup[] $studentGroups */
        $studentGroups = $this->grouper->group(
            $studentRepository->findAll(),
            StudentGradeStrategy::class
        );
        $this->sorter->sort($studentGroups, StudentGradeGroupStrategy::class);
        $this->sorter->sortGroupItems($studentGroups, StudentStrategy::class);

        return $this->render('lists/study_groups.html.twig', [
            'grades' => $grades,
            'studentGroups' => $studentGroups
        ]);
    }

}