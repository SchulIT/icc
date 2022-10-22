<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class InfotextData {

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    #[Assert\NotNull]
    private ?DateTime $date = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $content = null;

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): InfotextData {
        $this->date = $date;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $content): InfotextData {
        $this->content = $content;
        return $this;
    }
}