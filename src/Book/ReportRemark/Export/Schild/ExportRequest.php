<?php

namespace App\Book\ReportRemark\Export\Schild;

use App\Common\Entity\Section;
use Symfony\Component\Validator\Constraints as Assert;

class ExportRequest {
    #[Assert\NotNull]
    public Section|null $section = null;

    #[Assert\NotBlank]
    public string|null $filename = 'SchuelerLELS.dat';
}
