<?php

namespace App\Import;

use App\Entity\Infotext;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\InfotextData;
use App\Request\Data\InfotextsData;

class InfotextsImportStrategy implements ReplaceImportStrategyInterface {

    private $repository;

    public function __construct(InfotextRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }

    public function removeAll(): void {
        $this->repository->removeAll();
    }

    /**
     * @param InfotextData $data
     */
    public function persist($data): void {
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
}