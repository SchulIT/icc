<?php

namespace App\Untis;

use App\Import\ExamsImportStrategy;
use App\Import\Importer;
use App\Request\Data\ExamData;
use App\Request\Data\ExamsData;
use App\Request\Data\ExamTuition;
use App\Settings\UntisSettings;
use DateTime;
use League\Csv\Reader;

class GpuExamImporter {
    private $importer;
    private $strategy;
    private $examReader;
    private $tuitionReader;
    private $settings;

    public function __construct(Importer $importer, ExamsImportStrategy $strategy, GpuExamReader $examReader, GpuTuitionReader $tuitionReader, UntisSettings $settings) {
        $this->importer = $importer;
        $this->strategy = $strategy;
        $this->examReader = $examReader;
        $this->tuitionReader = $tuitionReader;
        $this->settings = $settings;
    }

    public function import(Reader $examReader, Reader $tuitionReader, DateTime $start, DateTime $end, bool $suppressNotifications) {
        $gpuExams = $this->examReader->readGpu($examReader);
        $tuitionMap = $this->computeTuitionMap($this->tuitionReader->readGpu($tuitionReader));

        $data = new ExamsData();
        $data->setSuppressNotifications($suppressNotifications);
        $exams = [ ];

        foreach($gpuExams as $gpuExam) {
            if($gpuExam->getDate() < $start || $gpuExam->getDate() > $end) {
                continue;
            }

            $exam = new ExamData();
            $exam->setId($gpuExam->getId());
            $exam->setDate($gpuExam->getDate());
            $exam->setLessonStart($gpuExam->getLessonStart());
            $exam->setLessonEnd($gpuExam->getLessonEnd());
            $exam->setRooms($gpuExam->getRooms());
            $exam->setStudents($gpuExam->getStudents());
            $exam->setSupervisions($gpuExam->getSupervisions());
            $exam->setDescription($gpuExam->getText());

            $this->clearStudentsIfNecessary($exam, $gpuExam->getName());

            $examTuition = $gpuExam->getTuitions();
            $examSubjects = $gpuExam->getSubjects();

            $examTuitionData = [ ];

            for($idx = 0; $idx < min(count($examTuition), count($examSubjects)); $idx++) {
                $tuition = $this->getTuition($tuitionMap, $examTuition[$idx], $examSubjects[$idx]);

                if($tuition !== null && !empty($tuition->getGrade()) && !empty($tuition->getTeacher()) && !empty($tuition->getSubject())) {
                    $examTuitionData[] = (new ExamTuition())
                        ->setGrades([$tuition->getGrade()])
                        ->setTeachers([$tuition->getTeacher()])
                        ->setSubjectOrCourse($tuition->getSubject());
                }
            }

            $exam->setTuitions($examTuitionData);

            $exams[] = $exam;
        }

        $data->setExams($exams);
        return $this->importer->import($data, $this->strategy);
    }

    private function clearStudentsIfNecessary(ExamData $data, ?string $name): ExamData {
        $ignoreOption = !empty($this->settings->getIgnoreStudentOptionRegExp())
            && $name !== null
            && preg_match('~' . $this->settings->getIgnoreStudentOptionRegExp() . '~i', $name);

        if(($this->settings->alwaysImportExamWriters() === false && $ignoreOption === false)
         ||($this->settings->alwaysImportExamWriters() === true && $ignoreOption === true)) {
            $data->setStudents([]);
        }

        return $data;
    }

    private function getTuition(array $map, int $id, string $subject): ?GpuTuition {
        if(!array_key_exists($id, $map)) {
            return null;
        }

        /** @var GpuTuition $gpuTuition */
        foreach($map[$id] as $gpuTuition) {
            if($gpuTuition->getSubject() === $subject) {
                return $gpuTuition;
            }
        }

        return null;
    }

    /**
     * @param GpuTuition[] $gpuTuitions
     * @return array
     */
    private function computeTuitionMap(array $gpuTuitions): array {
        $map = [ ];

        foreach($gpuTuitions as $gpuTuition) {
            if(!array_key_exists($gpuTuition->getId(), $map)) {
                $map[$gpuTuition->getId()] = [];
            }

            $map[$gpuTuition->getId()][] = $gpuTuition;
        }

        return $map;
    }
}