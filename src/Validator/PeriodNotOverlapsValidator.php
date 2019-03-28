<?php

namespace App\Validator;

use App\Entity\TimetablePeriod;
use App\Repository\TimetablePeriodRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PeriodNotOverlapsValidator extends ConstraintValidator {

    private $timetablePeriodRepository;

    public function __construct(TimetablePeriodRepositoryInterface $timetablePeriodRepository) {
        $this->timetablePeriodRepository = $timetablePeriodRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$value instanceof TimetablePeriod) {
            throw new UnexpectedTypeException($value, TimetablePeriod::class);
        }

        if(!$constraint instanceof PeriodNotOverlaps) {
            throw new UnexpectedTypeException($constraint, PeriodNotOverlaps::class);
        }

        $periods = $this->timetablePeriodRepository
            ->findAll();

        foreach($periods as $period) {
            if($period->getId() === $value->getId()) {
                continue; // do not compare with itself
            }

            if($this->overlap($period, $value)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ key }}', $period->getExternalId())
                    ->addViolation();
            }
        }
    }

    /**
     * Tests if two given periods overlap (see https://stackoverflow.com/a/325964)
     *
     * @param TimetablePeriod $periodA
     * @param TimetablePeriod $periodB
     * @return boolean
     */
    private function overlap(TimetablePeriod $periodA, TimetablePeriod $periodB) {
        $startA = $periodA->getStart()->getTimestamp();
        $startB = $periodB->getStart()->getTimestamp();

        $endA = $periodA->getEnd()->getTimestamp();
        $endB = $periodB->getEnd()->getTimestamp();

        return max($startA, $startB) <= min($endA, $endB);
    }
}