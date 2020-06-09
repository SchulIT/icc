<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;

class TimetablePeriodList {

    /**
     * @Serializer\SerializedName("periods")
     * @Serializer\Type("array<App\Response\Api\V1\TimetablePeriod>")
     * @var TimetablePeriod[]
     */
    private $periods;

    /**
     * @return TimetablePeriod[]
     */
    public function getPeriods(): array {
        return $this->periods;
    }

    /**
     * @param TimetablePeriod[] $periods
     * @return TimetablePeriodList
     */
    public function setPeriods(array $periods): TimetablePeriodList {
        $this->periods = $periods;
        return $this;
    }
}