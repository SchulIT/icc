<?php

namespace App\Tests\Converter;

use App\Converter\TeacherStringConverter;
use App\Entity\Gender;
use App\Entity\Teacher;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeacherConverterTest extends WebTestCase {

    private function getTranslator(): TranslatorInterface {
        $kernel = static::createKernel();
        $kernel->boot();
        $translator = $kernel->getContainer()->get('translator');
        $translator->setLocale('de');

        return $translator;
    }

    public function testMale() {
        $teacher = (new Teacher())
            ->setAcronym('MUSM')
            ->setGender(Gender::Male())
            ->setFirstname('Max')
            ->setLastname('Mustermann')
            ->setTitle(null);
        $converter = new TeacherStringConverter($this->getTranslator());
        $this->assertEquals('Herr Mustermann [MUSM]', $converter->convert($teacher, true));
    }

    public function testFemale() {
        $teacher = (new Teacher())
            ->setAcronym('MUSF')
            ->setGender(Gender::Female())
            ->setFirstname('Erika')
            ->setLastname('Musterfrau')
            ->setTitle('Dr.');
        $converter = new TeacherStringConverter($this->getTranslator());
        $this->assertEquals('Frau Dr. Musterfrau [MUSF]', $converter->convert($teacher, true));
    }

    public function testX() {
        $teacher = (new Teacher())
            ->setAcronym('DOEX')
            ->setGender(Gender::X())
            ->setLastname('Doe');
        $converter = new TeacherStringConverter($this->getTranslator());
        $this->assertEquals('Doe [DOEX]', $converter->convert($teacher, true));
    }
}