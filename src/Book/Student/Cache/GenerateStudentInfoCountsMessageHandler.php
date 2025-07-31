<?php

namespace App\Book\Student\Cache;

use App\Entity\Grade;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Repository\GradeRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GenerateStudentInfoCountsMessageHandler {

    public function __construct(private StudentInfoCountsGenerator $studentInfoCountsGenerator,
                                private StudentRepositoryInterface $studentRepository,
                                private TeacherRepositoryInterface $teacherRepository,
                                private GradeRepositoryInterface $gradeRepository,
                                private TuitionRepositoryInterface $tuitionRepository,
                                private SectionRepositoryInterface $sectionRepository) {

    }

    public function __invoke(GenerateStudentInfoCountsMessage $message): void {
        $student = $this->studentRepository->findOneById($message->studentId);
        if($student === null) {
            return;
        }

        $section = $this->sectionRepository->findOneById($message->sectionId);
        if($section === null) {
            return;
        }

        $context = $this->getContext($message->contextType, $message->contextId);
        if($context === null) {
            return;
        }

        $this->studentInfoCountsGenerator->regenerate($student, $section, $context);
    }

    private function getContext(ContextType $type, int $id): Grade|Tuition|Teacher|null {
        return match ($type) {
            ContextType::Grade => $this->gradeRepository->findOneById($id),
            ContextType::Tuition => $this->tuitionRepository->findOneById($id),
            ContextType::Teacher => $this->teacherRepository->findOneById($id)
        };
    }
}