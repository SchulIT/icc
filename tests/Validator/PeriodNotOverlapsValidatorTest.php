<?php

namespace App\Tests\Validator;

use App\Entity\TimetablePeriod;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Validator\PeriodNotOverlaps;
use App\Validator\PeriodNotOverlapsValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PeriodNotOverlapsValidatorTest extends ConstraintValidatorTestCase {

    public function getValidPeriods() {
        return [
            [ (new TimetablePeriod())->setId(10)->setStart(new \DateTime('2019-12-01'))->setEnd(new \DateTime('2018-12-31'))],
            [ (new TimetablePeriod())->setId(11)->setStart(new \DateTime('2019-03-01'))->setEnd(new \DateTime('2019-04-01'))]
        ];
    }

    public function getInvalidPeriods() {
        return [
            [ (new TimetablePeriod())->setId(12)->setStart(new \DateTime('2018-12-30'))->setEnd(new \DateTime('2019-01-10')), ['january']],
            [ (new TimetablePeriod())->setId(13)->setStart(new \DateTime('2019-01-01'))->setEnd(new \DateTime('2019-01-10')), ['january']],
            [ (new TimetablePeriod())->setId(14)->setStart(new \DateTime('2019-01-10'))->setEnd(new \DateTime('2019-01-20')), ['january']],
            [ (new TimetablePeriod())->setId(15)->setStart(new \DateTime('2019-01-29'))->setEnd(new \DateTime('2019-02-10')), ['january', 'february']],
            [ (new TimetablePeriod())->setId(16)->setStart(new \DateTime('2019-02-20'))->setEnd(new \DateTime('2019-02-28')), ['february']],
        ];
    }

    /**
     * @dataProvider getValidPeriods
     */
    public function testValidPeriod(TimetablePeriod $period) {
        $constraint = new PeriodNotOverlaps();
        $this->validator->validate($period, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidPeriods
     */
    public function testInvalidPeriod(TimetablePeriod $period, array $errorKeys) {
        $constraint = new PeriodNotOverlaps();
        $this->validator->validate($period, $constraint);

        $assertions = $this->buildViolation($constraint->message)
            ->setParameter('{{ key }}', $errorKeys[0]);

        for($i = 1; $i < count($errorKeys); $i++) {
            $assertions = $assertions
                ->buildNextViolation($constraint->message)
                ->setParameter('{{ key }}', $errorKeys[$i]);
        }

        $assertions->assertRaised();
    }

    /**
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testInvalidConstraint() {
        $constraint = new NotBlank();
        $this->validator->validate(new TimetablePeriod(), $constraint);
    }

    /**
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testInvalidValue() {
        $constraint = new PeriodNotOverlaps();
        $this->validator->validate(new \stdClass(), $constraint);
    }

    public function testExistingPeriodDoesNotOverlapItself() {
        $constraint = new PeriodNotOverlaps();
        $period = $this->getPeriods()[0];

        $this->validator->validate($period, $constraint);
        $this->assertNoViolation();
    }

    private function getPeriods() {
        $periods = [ ];

        $periods[] = (new TimetablePeriod())
            ->setId(1)
            ->setExternalId('january')
            ->setStart(new \DateTime('2019-01-01'))
            ->setEnd(new \DateTime('2019-01-31'));

        $periods[] = (new TimetablePeriod())
            ->setId(2)
            ->setExternalId('february')
            ->setStart(new \DateTime('2019-02-01'))
            ->setEnd(new \DateTime('2019-02-28'));

        return $periods;
    }

    protected function createValidator() {
        $repository = $this->createMock(TimetablePeriodRepositoryInterface::class);
        $repository
            ->method('findAll')
            ->willReturn($this->getPeriods());

        return new PeriodNotOverlapsValidator($repository);
    }
}