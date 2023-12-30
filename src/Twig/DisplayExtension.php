<?php

namespace App\Twig;

use App\Display\GradeGroup;
use App\Display\TeacherGroup;
use InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DisplayExtension extends AbstractExtension {
    public function getFunctions(): array {
        return [
            new TwigFunction('divide_groups', [ $this, 'divideGroups' ])
        ];
    }

    /**
     * @param GradeGroup[]|TeacherGroup[] $groups
     * @param int $columns
     * @return array
     */
    public function divideGroups(array $groups, int $columns): array {
        if($columns === 0) {
            throw new InvalidArgumentException('Number of columns must be greater than 0');
        }

        $result = [ ];

        if(count($groups) === 0) {
            return [ ];
        }

        $totalCount = 0;

        foreach($groups as $group) {
            $totalCount += count($group->getItems());
        }

        $avgPerColumn = (float)$totalCount / $columns;
        $currentGroupIdx = 0;
        $currentGroupItemCount = 0;

        foreach($groups as $group) {
            if(!array_key_exists($currentGroupIdx, $result)) {
                $result[$currentGroupIdx] = [ ];
            }

            $result[$currentGroupIdx][] = $group;
            $currentGroupItemCount += count($group->getItems());

            if($currentGroupItemCount >= ($currentGroupIdx + 1)*$avgPerColumn) {
                $currentGroupIdx++;
            }
        }

        return $result;
    }
}