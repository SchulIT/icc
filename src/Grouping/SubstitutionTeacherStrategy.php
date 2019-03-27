<?php

namespace App\Grouping;

use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Repository\TeacherRepositoryInterface;

class SubstitutionTeacherStrategy implements GroupingStrategyInterface {

    /** @var Teacher[] */
    private $teachers = null;

    private $teacherRepository;

    public function __construct(TeacherRepositoryInterface $teacherRepository) {
        $this->teacherRepository = $teacherRepository;
    }


    private function loadTeachers() {
        if($this->teachers === null) {
            $teachers = $this->teacherRepository->findAll();

            $this->teachers = [ ];

            foreach($teachers as $teacher) {
                $this->teachers[$teacher->getAcronym()] = $teacher;
            }
        }
    }

    /**
     * @param Substitution $object
     * @return Teacher[]
     */
    public function computeKey($object) {
        $this->loadTeachers();

        /** @var Teacher[] $teachers */
        $teachers = [ ];

        if($object->getTeacher() !== null) {
            $teachers[] = $object->getTeacher();
        }

        if($object->getReplacementTeacher() !== null) {
            $teachers[] = $object->getReplacementTeacher();
        }

        $regExp = '~(^|\s|\.|,|;|:|\()(' . implode('|', array_keys($this->teachers)) . ')($|\s|\.|,|;|:|\))~';

        if(preg_match_all($regExp, $object->getRemark(), $results, PREG_SET_ORDER)) {
            foreach($results as $result) {
                /**
                 * [0] => matched block
                 * [1] => (^|\s|\.|,|;|:|\()
                 * [2] => Acronym
                 * [3] => ($|\s|\.|,|;|:|\))
                 */
                $acronym = $result[2];

                if(!empty($acronym) && isset($this->teachers[$acronym])) {
                    $teachers[] = $this->teachers[$acronym];
                }
            }
        }

        return $teachers;
    }

    /**
     * @param Teacher|null $keyA
     * @param Teacher|null $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        if($keyA === null && $keyB === null) {
            return true;
        }

        return $keyA === $keyB || $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Teacher $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new SubstitutionTeacherGroup($key);
    }
}