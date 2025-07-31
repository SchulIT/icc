<?php

namespace App\Book\Student\Cache;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CacheWarmupHelper {

    public function __construct(private MessageBusInterface $messageBus,
                                private TuitionRepositoryInterface $tuitionRepository,
                                private StudentRepositoryInterface $studentRepository) {

    }

    /**
     * @throws ExceptionInterface
     */
    public function warmupGrade(Grade $grade, Section $section): int {
        $count = 0;

        foreach($this->studentRepository->findAllByGrade($grade, $section) as $student) {
            $message = new GenerateStudentInfoCountsMessage(
                $student->getId(),
                $section->getId(),
                ContextType::Grade,
                $grade->getId()
            );
            $this->messageBus->dispatch($message);
            $count++;
        }

        return $count;
    }

    /**
     * @throws ExceptionInterface
     */
    public function warmupTeacher(Teacher $teacher, Section $section): int {
        $count = 0;

        foreach($this->tuitionRepository->findAllByTeacher($teacher, $section) as $tuition) {
            foreach($this->studentRepository->findAllByTuition($tuition, includeStudentsWithAttendance: true) as $student) {
                $message = new GenerateStudentInfoCountsMessage(
                    $student->getId(),
                    $section->getId(),
                    ContextType::Teacher,
                    $teacher->getId()
                );
                $this->messageBus->dispatch($message);
                $count++;
            }
        }

        return $count;
    }

    /**
     * @throws ExceptionInterface
     */
    public function warmupTuition(Tuition $tuition, Section $section): int {
        $count = 0;

        foreach ($this->studentRepository->findAllByTuition($tuition, includeStudentsWithAttendance: true) as $student) {
            $message = new GenerateStudentInfoCountsMessage(
                $student->getId(),
                $section->getId(),
                ContextType::Tuition,
                $tuition->getId()
            );
            $this->messageBus->dispatch($message);
            $count++;
        }

        return $count;
    }
}