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
            $regExp = '~(^|\s|\.|,|;|:|\()(' . $teacher->getAcronym() . ')($|\s|\.|,|;|:|\))~';

            $content = preg_replace_callback($regExp, function($matches) use ($teacher) {
                return $matches[1] . $this->teacherConverter->convert($teacher) . $matches[3];
            }, $content);
        }

        return $content;
    }

    /**
     * @return Teacher[]
     */
    protected function getListOfTeachers() {
        if($this->cache === null) {
            $this->cache = $this->teacherRepository->findAll();
        }

        return $this->cache;
    }
}