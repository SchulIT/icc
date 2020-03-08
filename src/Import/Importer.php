<?php

namespace App\Import;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class Importer {

    private $validator;

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    /**
     * @param $data
     * @throws ValidationFailedException
     */
    private function validateOrThrow($data) {
        $violations = $this->validator->validate($data);

        if(count($violations) === 0) {
            // Validation passed
            return;
        }

        // Validation not passed
        $data = [ ];

        /** @var ConstraintViolation $violation */
        foreach($violations as $violation) {
            $data[] = [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ];
        }

        throw new ValidationFailedException($data, 'Input JSON failed validation.');
    }


    /**
     * @param $data
     * @param ImportStrategyInterface $strategy
     * @return ImportResult
     * @throws ImportException
     * @throws ValidationFailedException
     */
    public function import($data, ImportStrategyInterface $strategy): ImportResult {
        $this->validateOrThrow($data);

        try {
            if($strategy instanceof InitializeStrategyInterface) {
                $strategy->initialize();
            }

            $repository = $strategy->getRepository();
            $repository->beginTransaction();

            $currentEntities = $strategy->getExistingEntities();
            $updatedEntitiesIds = [];

            $addedEntities = [];
            $updatedEntities = [];
            $removedEntities = [];

            // ADD/UPDATE ENTITIES
            foreach ($data as $object) {
                $entity = $strategy->getExistingEntity($object, $currentEntities);

                if ($entity !== null) {
                    $updatedEntities[] = $entity;
                    $strategy->updateEntity($entity, $object);
                    $updatedEntitiesIds[] = $strategy->getEntityId($entity);
                } else {
                    $entity = $strategy->createNewEntity($object);
                    $addedEntities[] = $entity;
                }

                $strategy->persist($entity);
            }

            // REMOVE ENTITIES
            foreach ($currentEntities as $entity) {
                $id = $strategy->getEntityId($entity);

                if (!in_array($id, $updatedEntitiesIds)) {
                    $removedEntities[] = $entity;
                    $strategy->remove($entity);
                }
            }

            $repository->commit();

            return new ImportResult($addedEntities, $updatedEntities, $removedEntities);
        } catch (Throwable $e) {
            throw new ImportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $data
     * @param ReplaceImportStrategyInterface $strategy
     * @return ImportResult
     * @throws ImportException
     */
    public function replaceImport($data, ReplaceImportStrategyInterface $strategy): ImportResult {
        $this->validateOrThrow($data);

        try {
            if($strategy instanceof InitializeStrategyInterface) {
                $strategy->initialize();
            }

            $repository = $strategy->getRepository();
            $repository->beginTransaction();

            $strategy->removeAll();

            $addedEntities = [];

            foreach ($data as $object) {
                $strategy->persist($object);
                $addedEntities[] = $object;
            }

            $repository->commit();

            return new ImportResult($addedEntities, [], []);
        } catch (Throwable $e) {
            throw new ImportException($e->getMessage(), $e->getCode(), $e);
        }
    }
}