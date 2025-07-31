<?php

namespace App\Book\Student\Cache;

use App\Book\Student\StudentInfoResolver;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Repository\TuitionRepositoryInterface;
use DateTime;
use Psr\Cache\InvalidArgumentException;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class StudentInfoCountsGenerator {

    private const string KEY_PATTERN = 'book.student_info.counts.%d.s-%d.%s-%d';

    public const int LIFETIME_IN_SECONDS = 1800; // 30min

    public function __construct(private readonly CacheInterface $cache,
                                private readonly StudentInfoResolver $studentInfoResolver,
                                private readonly TuitionRepositoryInterface $tuitionRepository,
                                private readonly DateHelper $dateHelper) {     }

    private function getKey(Student $student, Section $section, Grade|Teacher|Tuition $context): string {
        return sprintf(
            self::KEY_PATTERN,
            $student->getId(),
            $section->getId(),
            strtolower(get_class($context)),
            $context->getId()
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function regenerate(Student $student, Section $section, Grade|Teacher|Tuition $context): StudentInfoCounts {
        $this->cache->delete($this->getKey($student, $section, $context));
        return $this->generate($student, $section, $context);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function generate(Student $student, Section $section, Grade|Teacher|Tuition $context): StudentInfoCounts {
        return $this->cache->get($this->getKey($student, $section, $context), function(ItemInterface $item) use ($student, $section, $context) {
            $item->expiresAfter(self::LIFETIME_IN_SECONDS);

            $tuitions = [ ];

            if($context instanceof Tuition) {
                $tuitions[] = $context;
            } else if($context instanceof Teacher) {
                $tuitions = $this->tuitionRepository->findAllByTeacher($context, $section);
            } else if($context instanceof Grade) {
                $tuitions = $this->tuitionRepository->findAllByGrades([ $context ], $section);
            }

            $info = $this->studentInfoResolver->resolveStudentInfo($student, $section, $tuitions, includeEvents: $context instanceof Grade);

            $flagCounts = [ ];

            foreach($info->getAttendanceFlagCounts() as $count) {
                $flagCounts[$count->getFlag()->getId()] = $count->getCount();
            }

            return new StudentInfoCounts(
                count($info->getComments()),
                $info->getLateMinutesCount(),
                $info->getAbsentLessonsCount(),
                $info->getTotalLessonsCount(),
                $info->getNotExcusedOrNotSetLessonsCount(),
                $info->getNotExcusedAbsentLessonsCount(),
                $flagCounts,
                $this->dateHelper->getNow()
            );
        });
    }
}