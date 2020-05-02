<?php

namespace App\Import;

use App\Request\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class Importer {

    private $validator;

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    /**
     * @param object $data
     * @throws ValidationFailedException
     */
    private function validateOrThrow($data) {
        $violations = $this->validator->validate($data);

        if(count($violations) === 0) {
            // Validation passed
            return;
        }

        throw new ValidationFailedException($violations);
    }


    /**
     * @param object $data
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

            $currentEntities = $strategy->getExistingEntities($data);
            $updatedEntitiesIds = [];

            $addedEntities = [];
            $updatedEntities = [];
            $removedEntities = [];

            $entities = $strategy->getData($data);

            // ADD/UPDATE ENTITIES
            foreach ($entities as $object) {
                $entity = $strategy->getExistingEntity($object, $currentEntities);

                if ($entity !== null) {
                    $updatedEntities[] = $entity;
                    $strategy->updateEntity($entity, $object, $data);
                    $updatedEntitiesIds[] = $strategy->getEntityId($entity);
                } else {
                    $entity = $strategy->createNewEntity($object, $data);
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
     * @param object $data
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

            $entities = $strategy->getData($data);

            foreach ($entities as $object) {
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