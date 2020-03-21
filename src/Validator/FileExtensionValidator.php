<?php

namespace App\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FileExtensionValidator extends ConstraintValidator {

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof FileExtension) {
            throw new UnexpectedTypeException($constraint, FileExtension::class);
        }

        if($value === null) {
            return;
        }

        if(!$value instanceof UploadedFile) {
            throw new UnexpectedTypeException($value, UploadedFile::class);
        }

        $extension = $value->getClientOriginalExtension();

        if(in_array($extension, $constraint->extensions) !== true) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ extensions }}', implode(', ', $constraint->extensions))
                ->addViolation();
        }
    }
}