<?php

namespace App\Repository;

use App\Entity\AppointmentCategory;

interface AppointmentCategoryRepositoryInterface {

    /**
     * @param int $id
     * @return AppointmentCategory|null
     */
    public function findOneById(int $id): ?AppointmentCategory;

    /**
     * @param string $externalId
     * @return AppointmentCategory|null
     */
    public function findOneByExternalId(string $externalId): ?AppointmentCategory;

    /**
     * @return AppointmentCategory[]
     */
    public function findAll();

    /**
     * @param AppointmentCategory $appointmentCategory
     */
    public function persist(AppointmentCategory $appointmentCategory): void;

    /**
     * @param AppointmentCategory $appointmentCategory
     */
    public function remove(AppointmentCategory $appointmentCategory): void;
}