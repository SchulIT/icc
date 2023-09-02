<?php

namespace App\Validator;

use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Utils\ArrayUtils;
use Countable;
use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CollectionNotEmptyValidator extends ConstraintValidator {

    public function __construct(private readonly PropertyAccessorInterface $propertyAccessor)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void {
        if(!$constraint instanceof CollectionNotEmpty) {
            throw new UnexpectedTypeException($constraint, CollectionNotEmpty::class);
        }

        if(!is_countable($value)) {
            throw new UnexpectedTypeException($value, Countable::class);
        }

        if(empty($constraint->propertyPath)) {
            throw new InvalidArgumentException('propertyPath must not empty.');
        }

        $userTypes = $this->propertyAccessor->getValue($this->context->getObject(), $constraint->propertyPath);

        if(!is_iterable($userTypes)) {
            throw new InvalidArgumentException('userTypes property is not iterable.');
        }

        foreach($userTypes as $userType) {
            if(!$userType instanceof UserTypeEntity) {
                throw new UnexpectedTypeException($userType, UserTypeEntity::class);
            }

            if(ArrayUtils::inArray($userType->getUserType(), [ UserType::Student, UserType::Parent] ) && count($value) === 0) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}