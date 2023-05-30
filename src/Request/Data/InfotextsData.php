<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class InfotextsData {

    use ContextTrait;

    /**
     * @var InfotextData[]
     */
    #[Assert\Valid]
    #[Serializer\Type('array<App\Request\Data\InfotextData>')]
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