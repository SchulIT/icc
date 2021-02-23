<?php

namespace App\Import;

use App\Entity\ImportDateTime;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Request\ValidationFailedException;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class Importer {

    private $validator;
    private $importDateTimeRepository;
    private $logger;

    public function __construct(ValidatorInterface $validator, ImportDateTypeRepositoryInterface $importDateTimeRepository, LoggerInterface $logger) {
        $this->validator = $validator;
        $this->importDateTimeRepository = $importDateTimeRepository;
        $this->logger = $logger;
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
            $ignoredEntities = [];

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

            $result = new ImportResult($addedEntities, $updatedEntities, $removedEntities, $ignoredEntities, $data);

            if($strategy instanceof PostActionStrategyInterface) {
                $strategy->onFinished($result);
            }

            return $result;
        } catch (Throwable $e) {
            $this->logger->error('Import failed.', [
                'exception' => $e
            ]);
            throw new ImportException($e->getMessage(), $e->getCode(), $e);
        } finally {
            $this->updateImportDateTime($strategy->getEntityClassName());
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
            $ignoredEntities = [];

            $entities = $strategy->getData($data);

            foreach ($entities as $object) {
                try {
                    $strategy->persist($object);
                    $addedEntities[] = $object;
                } catch (EntityIgnoredException $e) {
                    $ignoredEntities[] = $e->getEntity();
                }
            }

            $repository->commit();

            return new ImportResult($addedEntities, [], [], $ignoredEntities);
        } catch (Throwable $e) {
            throw new ImportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function updateImportDateTime(string $className): void {
        $dateTime = $this->importDateTimeRepository->findOneByEntityClass($className);

        if($dateTime === null) {
            $dateTime = (new ImportDateTime())
                ->setEntityClass($className);

        }

        $dateTime->setUpdatedAt(new DateTime('now'));

        $this->importDateTimeRepository->persist($dateTime);
    }
}