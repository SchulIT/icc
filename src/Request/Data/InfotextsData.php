<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class InfotextsData {

    use ContextTrait;

    /**
     * @Serializer\Type("array<App\Request\Data\InfotextData>")
     * @var InfotextData[]
     */
    #[Assert\Valid]
    private array $infotexts = [ ];

    /**
     * @return InfotextData[]
     */
    public function getInfotexts() {
        return $this->infotexts;
    }

    /**
     * @param InfotextData[] $infotexts
     */
    public function setInfotexts($infotexts): InfotextsData {
        $this->infotexts = $infotexts;
        return $this;
    }

}