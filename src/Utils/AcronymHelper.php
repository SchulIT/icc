<?php

namespace App\Utils;

use App\Converter\TeacherStringConverter;
use App\Entity\Teacher;
use App\Repository\TeacherRepositoryInterface;

class AcronymHelper {
    /**
     * @var Teacher[]|null
     */
    private $cache = null;

    private $teacherConverter;
    private $teacherRepository;

    public function __construct(TeacherStringConverter $teacherConverter, TeacherRepositoryInterface $teacherRepository) {
        $this->teacherConverter = $teacherConverter;
        $this->teacherRepository = $teacherRepository;
    }

    public function replaceAcronyms(string $content) {
        /** @var Teacher[] $teachers */
        $teachers = $this->getListOfTeachers();

        foreach($teachers as $teacher) {
            $regExp = '~\b' . $teacher->getAcronym() . '\b~';

            $content = preg_replace_callback($regExp, function($matches) use ($teacher) {
                return $this->teacherConverter->convert($teacher);
            }, $content);
        }

        return $content;
    }

    /**
     * @return Teacher[]
     */
    private function getListOfTeachers() {
        if($this->cache === null) {
            $this->cache = $this->teacherRepository->findAll();
        }

        return $this->cache;
    }
}