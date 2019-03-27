<?php

namespace App\Ics;

interface IcsItemInterface {

    public function getId();

    public function getStart(): \DateTime;

    public function getEnd(): \DateTime;

    public function isAllday(): bool;

    public function getLocation(): ?string;

    public function getSummary(): ?string;

    public function getDescription(): ?string;
}