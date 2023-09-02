<?php

namespace App\Validator;

use App\Entity\Room;
use App\Entity\Room as RoomEntity;
use App\Repository\ResourceRepositoryInterface;
use App\Request\Data\RoomData;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotAResourceValidator extends ConstraintValidator {

    public function __construct(private readonly ResourceRepositoryInterface $resourceRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void {
        if(!$constraint instanceof NotAResource) {
            throw new UnexpectedTypeException($constraint, NotAResource::class);
        }

        if($value instanceof RoomEntity) {
            $resource = $this->resourceRepository->findOneByName($value->getName());

            if($resource != null && $resource->getId() === $value->getId()) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ name }}', $value->getName())
                    ->addViolation();
            }
        } else if($value instanceof RoomData) {
            $resource = $this->resourceRepository->findOneByName($value->getName());

            if($resource != null && !$resource instanceof Room) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{ name }}', $value->getName())
                    ->addViolation();
            }
        }
    }
}