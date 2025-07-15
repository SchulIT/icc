<?php

namespace App\Exam\Bulk;

use App\Entity\Exam;
use App\Entity\ExamStudent;
use App\Entity\StudyGroupMembership;
use App\Repository\ExamRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;

readonly class ExamCreator {
    public function __construct(private ExamRepositoryInterface $repository, private TuitionRepositoryInterface $tuitionRepository) {

    }

    public function bulkCreate(BulkExamRequest $request): void {
        $this->repository->beginTransaction();

        $alreadyProcessedTuitionIds = [ ];

        foreach($request->grades as $grade) {
            foreach($request->subjects as $subject) {
                $tuitions = $this->tuitionRepository->findAllByGradeAndSubject($grade, $subject, $request->section);

                foreach($tuitions as $tuition) {
                    if(in_array($tuition->getId(), $alreadyProcessedTuitionIds)) {
                        continue;
                    }

                    $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();

                    for($i = 0; $i < $request->numberOfExams; $i++) {
                        $exam = new Exam();
                        $exam->setTuitionTeachersCanEditExam($request->canEdit);
                        $exam->addTuition($tuition);

                        if($request->addStudents === true) {
                            foreach ($students as $student) {
                                $exam->addStudent((new ExamStudent())->setStudent($student)->setTuition($tuition)->setExam($exam));
                            }
                        }

                        $this->repository->persist($exam);
                    }

                    $alreadyProcessedTuitionIds[] = $tuition->getId();
                }
            }
        }

        $this->repository->commit();
    }
}