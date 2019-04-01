<?php

namespace App\Tests\Validator;

use App\Entity\TimetablePeriod;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Validator\PeriodNotOverlaps;
use App\Validator\PeriodNotOverlapsValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PeriodNotOverlapsValidatorTest extends ConstraintValidatorTestCase {

    private function getPeriod(int $id, string $start, string $end) {
        $period = $this->createMock(TimetablePeriod::class);
        $period
            ->method('getId')
            ->willReturn($id);

        $period
            ->method('getStart')
            ->willReturn(new \DateTime($start));

        $period
            ->method('getEnd')
            ->willReturn(new \DateTime($end));

        return $period;
    }

    public function getValidPeriods() {
        return [
            [ $this->getPeriod(10, '2019-12-01', '2018-12-31') ],
            [ $this->getPeriod(11,'2019-03-01','2019-04-01') ]
        ];
    }

    public function getInvalidPeriods() {
        return [
            [ $this->getPeriod(12,'2018-12-30','2019-01-10'), ['january']],
            [ $this->getPeriod(13,'2019-01-01','2019-01-10'), ['january']],
            [ $this->getPeriod(14,'2019-01-10','2019-01-20'), ['january']],
            [ $this->getPeriod(15,'2019-01-29','2019-02-10'), ['january', 'february']],
            [ $this->getPeriod(16,'2019-02-20','2019-02-28'), ['february']],
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
            ->setExternalId('january')
            ->setStart(new \DateTime('2019-01-01'))
            ->setEnd(new \DateTime('2019-01-31'));

        $periods[] = (new TimetablePeriod())
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