<?php

namespace App\Controller;

use App\Book\Grade\Category;
use App\Book\Grade\GradeOverview;
use App\Book\Grade\GradeOverviewHelper;
use App\Book\Grade\GradePersister;
use App\Entity\Grade;
use App\Entity\GradeMembership;
use App\Entity\GradeTeacher;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\TuitionGradeCategory;
use App\Entity\User;
use App\Repository\TuitionRepositoryInterface;
use App\Settings\TuitionGradebookSettings;
use App\Utils\ArrayUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TuitionFilter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book/gradebook')]
class TuitionGradebookController extends AbstractController {

    public const NumberOfStudentsPerPage = 25;
    public const StudentPaginationThreshold = 35;

    #[Route('/keepalive', name: 'gradebook_keepalive')]
    public function keepAlive(Request $request): Response {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('', name: 'gradebook')]
    public function index(Request $request, TuitionFilter $tuitionFilter, StudentFilter $studentFilter,
                          SectionFilter $sectionFilter, TuitionRepositoryInterface $tuitionRepository,
                          GradeOverviewHelper $gradeOverviewHelper, TuitionGradebookSettings $gradebookSettings,
                          GradePersister $gradePersister, GradeFilter $gradeFilter): Response {
        /** @var User $user */
        $user = $this->getUser();
        
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user, true);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user, false);

        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);
        $ownGrades = $this->resolveOwnGrades($sectionFilterView->getCurrentSection(), $user);

        $overview = null;
        $page = $request->query->getInt('page', 1);
        if($page < 1) { $page = 1; }
        $pages = 1;

        if($tuitionFilterView->getCurrentTuition() !== null) {
            $overview = $gradeOverviewHelper->computeOverviewForTuition($tuitionFilterView->getCurrentTuition());
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $overview = $gradeOverviewHelper->computeOverviewForStudent($studentFilterView->getCurrentStudent(), $sectionFilterView->getCurrentSection());
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $gradeStudents = $gradeFilterView->getCurrentGrade()->getMemberships()->map(fn(GradeMembership $membership) => $membership->getStudent())->toArray();

            if(count($gradeStudents) > self::StudentPaginationThreshold) {
                $pages = ceil(count($gradeStudents) / (double)self::NumberOfStudentsPerPage);
                $gradeStudents = array_slice($gradeStudents, self::NumberOfStudentsPerPage * ($page - 1), self::NumberOfStudentsPerPage);
            }

            $overview = [ ];
            foreach($gradeStudents as $gradeStudent) {
                $overview[] = $gradeOverviewHelper->computeOverviewForStudent($gradeStudent, $sectionFilterView->getCurrentSection());
            }
        }

        if($request->isMethod('POST')) {
            if($this->isCsrfTokenValid('gradebook', $request->request->get('_csrf_token')) !== true) {
                $this->addFlash('error', 'CSRF token invalid.');
            } else {
                if($tuitionFilterView->getCurrentTuition() !== null) {
                    $gradePersister->persist($tuitionFilterView->getCurrentTuition(), $overview, $request->request->all('grades'));
                } else if($studentFilterView->getCurrentStudent() !== null) {
                    $gradePersister->persist($studentFilterView->getCurrentStudent(), $overview, $request->request->all('grades'));
                }

                $this->addFlash('success', 'book.grades.success');
                return $this->redirectToRequestReferer('gradebook');
            }
        }

        $categories = [ ];
        $hiddenCategories = [ ];

        if($overview !== null && !is_array($overview)) {
            $categories = ArrayUtils::createArrayWithKeys(
                $overview->getCategories(),
                fn(Category $category) => $category->getCategory()->getId()
            );
        } else if(is_array($overview)) {
            $categories = [ ];
            foreach($overview as $studentOverview) {
                foreach($studentOverview->getCategories() as $category) {
                    if(!array_key_exists($category->getCategory()->getId(), $categories)) {
                        $categories[$category->getCategory()->getId()] = $category;
                    }
                }
            }
        }

        if($overview !== null) {
            $categories = array_map(fn(Category $category) => $category->getCategory(), $categories);
            $hiddenCategories = $request->query->all('hide');
        }

        return $this->render('books/grades/overview.html.twig', [
            'sectionFilter' => $sectionFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'studentFilter' => $studentFilterView,
            'gradeFilter' => $gradeFilterView,
            'ownTuitions' => $ownTuitions,
            'ownGrades' => $ownGrades,
            'overview' => $overview,
            'key' => $gradebookSettings->getEncryptedMasterKey(),
            'ttl' => $gradebookSettings->getTtlForSessionStorage(),
            'categories' => $categories,
            'hiddenCategories' => $hiddenCategories,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    /**
     * @return Tuition[]
     */
    private function resolveOwnTuitions(?Section $currentSection, User $user, TuitionRepositoryInterface $tuitionRepository): array {
        if($currentSection === null) {
            return [ ];
        }

        $tuitions = [ ];

        if ($user->isStudentOrParent()) {
            $tuitions = $tuitionRepository->findAllByStudents($user->getStudents()->toArray(), $currentSection);
        } else if ($user->isTeacher()) {
            $tuitions = $tuitionRepository->findAllByTeacher($user->getTeacher(), $currentSection);
        }

        return array_filter($tuitions, fn(Tuition $tuition) => $tuition->getGradeCategories()->count() > 0);
    }

    /**
     * @return Grade[]
     */
    private function resolveOwnGrades(?Section $currentSection, User $user): array {
        if($currentSection === null) {
            return [ ];
        }

        if ($user->isStudentOrParent()) {
            return ArrayUtils::unique(
                $user->getStudents()->map(fn(Student $student) => $student->getGrade($currentSection))
            );
        } else if ($user->isTeacher()) {
            return $user->getTeacher()->getGrades()->
            filter(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getSection() === $currentSection)
                ->map(fn(GradeTeacher $gradeTeacher) => $gradeTeacher->getGrade())
                ->toArray();
        }

        return [ ];
    }
}