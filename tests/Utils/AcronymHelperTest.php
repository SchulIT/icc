<?php

namespace App\Tests\Utils;

use App\Converter\TeacherStringConverter as OriginalTeacherStringConverter;
use App\Entity\Gender;
use App\Entity\Teacher;
use App\Repository\TeacherRepositoryInterface;
use App\Utils\AcronymHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;

class AcronymHelperTest extends TestCase {

    private function getTeachers() {
        $teachers = [
            (new Teacher())->setAcronym('DOE')->setGender(Gender::Male())->setLastname('Doe')->setFirstname('John'),
            (new Teacher())->setAcronym('MUE')->setGender(Gender::Female())->setLastname('Mueller')->setFirstname('Laura')
        ];

        return $teachers;
    }

    public function testReplaceAcronyms() {
        $repository = $this->createMock(TeacherRepositoryInterface::class);
        $repository->method('findAll')
            ->willReturn($this->getTeachers());

        $acronymHelper = new AcronymHelper(new TeacherStringConverter(), $repository);
        $this->assertEquals('Mr. Doe hands exercises to Mrs. Mueller', $acronymHelper->replaceAcronyms('DOE hands exercises to MUE'));

    }
}

class TeacherStringConverter extends OriginalTeacherStringConverter {

    public function __construct() {
        parent::__construct(new IdentityTranslator());
    }

    public function convert(Teacher $teacher) {
        if($teacher->getGender()->equals(Gender::Male())) {
            return sprintf('Mr. %s', $teacher->getLastname());
        }

        return sprintf('Mrs. %s', $teacher->getLastname());
    }
}