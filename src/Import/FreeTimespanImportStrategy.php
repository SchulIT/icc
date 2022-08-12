<?php

namespace App\Import;

use App\Entity\FreeTimespan;
use App\Repository\FreeTimespanRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\FreeLessonTimespanData;
use App\Request\Data\FreeLessonTimespansData;

class FreeTimespanImportStrategy implements ReplaceImportStrategyInterface {

    use ContextAwareTrait;

    private FreeTimespanRepositoryInterface $repository;

    public function __construct(FreeTimespanRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return FreeTimespan::class;
    }

    /**
     * @param FreeLessonTimespansData $data
     * @return array
     */
    public function getData($data): array {
        return $data->getFreeLessons();
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }

    public function removeAll($requestData): void {
        $dateTime = $this->getContext($requestData);
        $this->repository->removeAll($dateTime);
    }

    /**
     * @param FreeLessonTimespanData $data
     */
    public function persist($data, $requestData): void {
        $freeTimespan = (new FreeTimespan())
            ->setStart($data->getStart())
            ->setEnd($data->getEnd())
            ->setDate($data->getDate());

        $this->repository->persist($freeTimespan);
    }
}