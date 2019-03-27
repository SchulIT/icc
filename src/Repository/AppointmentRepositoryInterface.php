<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\UserType;

interface AppointmentRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Appointment|null
     */
    public function findOneById(int $id): ?Appointment;

    /**
     * @param string $externalId
     * @return Appointment|null
     */
    public function findOneByExternalId(string $externalId): ?Appointment;

    /**
     * @param UserType $userType
     * @param \DateTime|null $today = null
     * @param Grade|null $grade
     * @return Appointment[]
     */
    public function findAllFor(UserType $userType, ?\DateTime $today = null, ?Grade $grade = null);

    /**
     * @param \DateTime|null $today = null
     * @return Appointment[]
     */
    public function findAll(?\DateTime $today = null);

    /**
     * @param Appointment $appointment
     */
    public function persist(Appointment $appointment): void;

    /**
     * @param Appointment $appointment
     */
    public function remove(Appointment $appointment): void;
}