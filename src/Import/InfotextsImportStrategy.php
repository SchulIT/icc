<?php

namespace App\Import;

use App\Entity\Infotext;
use App\Repository\InfotextRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\InfotextsData;

class InfotextsImportStrategy implements RelationsImportStrategyInterface {

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
     * @param InfotextsData $data
     */
    public function persist($data): void {
        foreach($data->getInfotexts() as $data) {
            $infotext = (new Infotext())
                ->setContent($data->getContent())
                ->setDate($data->getDate());

            $this->repository->persist($infotext);
        }
    }
}