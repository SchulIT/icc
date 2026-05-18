<?php

namespace App\Substitution\Import\Json;

use App\Framework\Import\Json\ContextTrait;
use App\Substitution\Import\Json\InfotextData;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class InfotextsData {

    use ContextTrait;

    /**
     * @var InfotextData[]
     */
    #[Assert\Valid]
    #[Serializer\Type('array<' . InfotextData::class . '>')]
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