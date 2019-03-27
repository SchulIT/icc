<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\Grade;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;

class AppointmentRepository implements AppointmentRepositoryInterface {
    private $em;
    private $isTransactionActive = false;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @param int $id
     * @return Appointment|null
     */
    public function findOneById(int $id): ?Appointment {
        return $this->em->getRepository(Appointment::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @param string $externalId
     * @return Appointment|null
     */
    public function findOneByExternalId(string $externalId): ?Appointment {
        return $this->em->getRepository(Appointment::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    /**
     * @param UserType $userType
     * @param \DateTime|null $today
     * @param Grade|null $grade
     * @return Appointment[]
     */
    public function findAllFor(UserType $userType, ?\DateTime $today = null, ?Grade $grade = null) {
        // TODO: Implement findAllFor() method.
    }

    /**
     * @param \DateTime|null $today
     * @return Appointment[]
     */
    public function findAll(?\DateTime $today = null) {
        // TODO: Implement findAll() method.
    }

    /**
     * @param Appointment $appointment
     */
    public function persist(Appointment $appointment): void {
        $this->em->persist($appointment);
        $this->isTransactionActive || $this->em->flush();
    }

    /**
     * @param Appointment $appointment
     */
    public function remove(Appointment $appointment): void {
        $this->em->remove($appointment);
        $this->isTransactionActive || $this->em->flush();
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
        $this->isTransactionActive = true;
    }

    public function commit(): void {
        $this->em->flush();
        $this->em->commit();
        $this->isTransactionActive = false;
    }
}