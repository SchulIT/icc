<?php

namespace App\Tools\WestermannZvs;

use App\LearningManagementSystem\Repository\StudentLearningManagementSystemInformationRepository;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudentStrategy;
use App\Tools\WestermannZvs\Check\Action;
use App\Tools\WestermannZvs\Check\CheckInterface;
use App\Framework\Utils\ArrayUtils;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class WestermannZvsChecker {
    public function __construct(
        /** @var CheckInterface[] */
        #[AutowireIterator(CheckInterface::AUTOCONFIGURE_KEY)] private iterable $checks,
        private SerializerInterface $serializer,
        private SectionResolverInterface $sectionResolver,
        private StudentRepositoryInterface $studentRepository,
        private StudentLearningManagementSystemInformationRepository $studentLearningManagementSystemInformationRepository,
        private Sorter $sorter
    ) {

    }

    public function check(CheckRequest $checkRequest): Result {
        /** @var array<string, Schueler> $zvsStudents */
        $zvsStudents = ArrayUtils::createArrayWithKeys(
            $this->serializer->deserialize($checkRequest->json, 'array<string, ' . Schueler::class . '>', 'json'),
            fn(Schueler $schueler): string => $schueler->username
        );

        $students = $this->studentRepository->findAllBySection($this->sectionResolver->getCurrentSection());
        $this->sorter->sort($students, StudentStrategy::class);

        $result = new Result();

        // All Students
        foreach($students as $student) {
            $username = $student->getEmail();

            $zvsStudent = $zvsStudents[$username] ?? null;

            $match = new StudentMatch();
            $match->username = $username;
            $match->schueler = $zvsStudent;
            $match->student = $student;
            $match->isConsented = $this->studentLearningManagementSystemInformationRepository->isConsentedByStudentAndLms($student, $checkRequest->lms);
            $match->isPasswordSet = $this->studentLearningManagementSystemInformationRepository->isPasswordSetByStudentAndLms($student, $checkRequest->lms);

            foreach($this->checks as $check) {
                $action = $check->needAction($match);

                if($action !== null) {
                    $match->actions[] = $action;
                }
            }

            $result->matches[$username] = $match;

            if(count($match->actions) > 0) {
                $result->needsAction[] = $match;
                $match->needsAction = true;
            }
        }

        // Missing students
        foreach($zvsStudents as $username => $zvsStudent) {
            if(array_key_exists($username, $result->matches)) {
                continue;
            }

            $match = new StudentMatch();
            $match->username = $username;
            $match->schueler = $zvsStudent;
            $match->student = null;
            $match->isConsented = false;
            $match->needsAction = true;
            $match->actions[] = new Action('checks.westermann_zsv.student_missing');

            $result->matches[$username] = $match;
            $result->needsAction[] = $match;
        }

        return $result;
    }
}