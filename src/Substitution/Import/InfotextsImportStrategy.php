<?php

namespace App\Substitution\Import;

use App\Framework\Import\ReplaceImportStrategyInterface;
use App\Framework\Import\ContextAwareTrait;
use App\Substitution\Entity\Infotext;
use App\Substitution\Repository\InfotextRepositoryInterface;
use App\Framework\Repository\TransactionalRepositoryInterface;
use App\Request\Data\InfotextData;
use App\Request\Data\InfotextsData;

class InfotextsImportStrategy implements ReplaceImportStrategyInterface {

    use ContextAwareTrait;

    public function __construct(private InfotextRepositoryInterface $repository)
    {
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }

    public function removeAll($requestData): void {
        $dateTime = $this->getContext($requestData);
        $this->repository->removeAll($dateTime);
    }

    /**
     * @param InfotextData $data
     */
    public function persist($data, $requestData): void {
        $infotext = (new Infotext())
            ->setContent($data->getContent())
            ->setDate($data->getDate());

        $this->repository->persist($infotext);
    }

    /**
     * @param InfotextsData $data
     * @return InfotextData[]
     */
    public function getData($data): array {
        return $data->getInfotexts();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Infotext::class;
    }
}