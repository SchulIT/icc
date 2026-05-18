<?php

namespace App\Common\Validator;

use App\Common\Entity\Room;
use App\Common\Entity\Room as RoomEntity;
use App\Common\Repository\ResourceRepositoryInterface;
use App\Common\Import\Json\RoomData;
use App\Common\Validator\NotAResource;
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

        if($value instanceof Room) {
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