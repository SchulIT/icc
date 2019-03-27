<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class TimetablePeriodVisibility {

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="TimetablePeriod", inversedBy="visibilities", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var TimetablePeriod
     */
    private $period;

    /**
     * @ORM\Id()
     * @ORM\Column(type="UserType::class")
     * @ORM\OrderBy("asc")
     * @var UserType
     */
    private $userType;

    /**
     * @return TimetablePeriod
     */
    public function getPeriod(): TimetablePeriod {
        return $this->period;
    }

    /**
     * @param TimetablePeriod $period
     * @return TimetablePeriodVisibility
     */
    public function setPeriod(TimetablePeriod $period): TimetablePeriodVisibility {
        $this->period = $period;
        return $this;
    }

    /**
     * @return UserType
     */
    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @param UserType $userType
     * @return TimetablePeriodVisibility
     */
    public function setUserType(UserType $userType): TimetablePeriodVisibility {
        $this->userType = $userType;
        return $this;
    }
}