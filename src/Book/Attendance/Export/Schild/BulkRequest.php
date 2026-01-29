<?php

namespace App\Book\Attendance\Export\Schild;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class BulkRequest {

    /**
     * @var Request[]
     */
    #[Assert\Count(max: 50)]
    #[Assert\Valid]
    #[Serializer\Type('array<' . Request::class .'>')]
    public array $requests = [ ];
}