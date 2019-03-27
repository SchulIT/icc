<?php

namespace App\Ics;

class IcsItem implements IcsItemInterface {

    /**
     * mixed
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var bool
     */
    private $isAllday;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $description;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return IcsItem
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return IcsItem
     */
    public function setStart(\DateTime $start): IcsItem {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return IcsItem
     */
    public function setEnd(\DateTime $end): IcsItem {
        $this->end = $end;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllday(): bool {
        return $this->isAllday;
    }

    /**
     * @param bool $isAllday
     * @return IcsItem
     */
    public function setIsAllday(bool $isAllday): IcsItem {
        $this->isAllday = $isAllday;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocation(): ?string {
        return $this->location;
    }

    /**
     * @param string $location
     * @return IcsItem
     */
    public function setLocation(string $location): IcsItem {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary(): ?string {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return IcsItem
     */
    public function setSummary(string $summary): IcsItem {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string $description
     * @return IcsItem
     */
    public function setDescription(string $description): IcsItem {
        $this->description = $description;
        return $this;
    }
}