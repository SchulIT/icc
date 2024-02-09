<?php

namespace App\View\Filter;

use App\Entity\ParentsDay;
use App\Entity\User;
use App\Repository\ParentsDayRepositoryInterface;
use App\Utils\ArrayUtils;

class ParentsDayFilter {

    public function __construct(private readonly ParentsDayRepositoryInterface $repository) {

    }

    public function handle(?string $uuid, User $user): ParentsDayFilterView {
        $parentsDays = ArrayUtils::createArrayWithKeys(
            $this->repository->findAll(),
            fn(ParentsDay $parentsDay) => $parentsDay->getUuid()->toString()
        );

        $parentsDay = $uuid !== null ?
            $parentsDays[$uuid] ?? null : null;

        $parentsDays = array_values($parentsDays);

        if($parentsDay === null && count($parentsDays) > 0) {
            $parentsDay = $parentsDays[0];
        }

        return new ParentsDayFilterView($parentsDays, $parentsDay);
    }
}