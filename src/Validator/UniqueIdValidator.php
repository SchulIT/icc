<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueIdValidator extends ConstraintValidator {

    public function __construct(private readonly PropertyAccessorInterface $propertyAccessor)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void {
        if(!$constraint instanceof UniqueId) {
            throw new UnexpectedTypeException($constraint, UniqueId::class);
        }

        if(!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        $ids = [ ];

        foreach($value as $item) {
            $id = $this->propertyAccessor->getValue($item, $constraint->propertyPath);

            if(in_array($id, $ids)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ id }}', $id)
                    ->addViolation();
            } else {
                $ids[] = $id;
            }
        }
    }
}