<?php

namespace App\Import;

use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;
use App\Repository\GradeRepositoryInterface;
use App\Repository\GradeTeacherRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\GradeTeacherData;
use App\Request\Data\GradeTeachersData;

class GradeTeachersImportStrategy implements ReplaceImportStrategyInterface {

    private $gradeTeacherRepository;
    private $gradeRepository;
    private $teacherRepository;

    public function __construct(GradeTeacherRepositoryInterface $gradeTeacherRepository, GradeRepositoryInterface $gradeRepository,
                                TeacherRepositoryInterface $teacherRepository) {
        $this->gradeTeacherRepository = $gradeTeacherRepository;
        $this->gradeRepository = $gradeRepository;
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->gradeTeacherRepository;
    }

    public function removeAll(): void {
        $this->gradeTeacherRepository->removeAll();
    }

    /**
     * @param GradeTeacherData $data
     * @throws ImportException
     */
    public function persist($data): void {
        $teacher = $this->teacherRepository->findOneByExternalId($data->getTeacher());

        if($teacher === null) {
            throw new ImportException(sprintf('Teacher with ID "%s" was not found.', $data->getTeacher()));
        }

        $grade = $this->gradeRepository->findOneByExternalId($data->getGrade());

        if($grade === null) {
            throw new ImportException(sprintf('Grade "%s" was not found.', $data->getGrade()));
        }

        $gradeTeacher = (new GradeTeacher())
            ->setTeacher($teacher)
            ->setGrade($grade)
            ->setType(new GradeTeacherType($data->getType()));

        $this->gradeTeacherRepository->persist($gradeTeacher);
    }

    /**
     * @param GradeTeachersData $data
     * @return GradeTeacherData[]
     */
    public function getData($data): array {
        return $data->getGradeTeachers();
    }
}