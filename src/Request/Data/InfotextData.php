<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class InfotextData {

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     * @Assert\Date()
     * @Assert\NotNull()
     * @var \DateTime
     */
    private $date;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $content;

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date
     * @return InfotextData
     */
    public function setDate(?\DateTime $date): InfotextData {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return InfotextData
     */
    public function setContent(?string $content): InfotextData {
        $this->content = $content;
        return $this;
    }
}