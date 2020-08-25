<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class InfotextsData {

    /**
     * @Serializer\Type("array<App\Request\Data\InfotextData>")
     * @Assert\Valid()
     * @var InfotextData[]
     */
    private $infotexts = [ ];

    /**
     * @return InfotextData[]
     */
    public function getInfotexts() {
        return $this->infotexts;
    }

    /**
     * @param InfotextData[] $infotexts
     * @return InfotextsData
     */
    public function setInfotexts($infotexts): InfotextsData {
        $this->infotexts = $infotexts;
        return $this;
    }

}