<?php

namespace App\ParentsDay\View\Filter;

use App\ParentsDay\Entity\ParentsDay;
use App\Common\Entity\User;
use App\ParentsDay\Repository\ParentsDayRepositoryInterface;
use App\Framework\Utils\ArrayUtils;
use App\ParentsDay\View\Filter\ParentsDayFilterView;

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