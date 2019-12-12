<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;

class UserRepository extends AbstractRepository implements UserRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?User {
        return $this->em->getRepository(User::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByUsername(string $username): ?User {
        return $this->em->getRepository(User::class)
            ->findOneBy([
                'username' => $username
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByEmail(string $email): ?User {
        return $this->em->getRepository(User::class)
            ->findOneBy([
                'email' => $email
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByStudent(Student $student) {
        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('uInner.id')
            ->from(User::class, 'uInner')
            ->leftJoin('uInner.student', 'sInner')
            ->where('sInner.id = :student');

        $qb
            ->select(['u', 's'])
            ->from(User::class, 'u')
            ->leftJoin('u.student', 's')
            ->where($qb->expr()->in('u.id', $qbInner->getDQL()))
            ->setParameter('student', $student->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(User::class)
            ->findBy([], [
                'username' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(User $user): void {
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(User $user): void {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function findAllByNotifyExams() {
        // TODO: Implement findAllByNotifyExams() method.
    }

    /**
     * @inheritDoc
     */
    public function findAllByNotifySubstitutions() {
        // TODO: Implement findAllByNotifySubstitutions() method.
    }

    /**
     * @inheritDoc
     */
    public function findAllByNotifyMessages() {
        // TODO: Implement findAllByNotifyMessages() method.
    }

    /**
     * @inheritDoc
     */
    public function findAllByNotifyTimetable() {
        // TODO: Implement findAllByNotifyTimetable() method.
    }

    /**
     * @inheritDoc
     */
    public function findAllByUserTypes(array $types): array {
        $typeNames = array_map(function(UserType $type) {
            return $type->getValue();
        }, $types);

        $qb = $this->em->createQueryBuilder();

        return $qb
            ->select(['u', 's'])
            ->from(User::class, 'u')
            ->leftJoin('u.students', 's')
            ->where($qb->expr()->in('u.userType', ':types'))
            ->setParameter('types', $typeNames)
            ->getQuery()
            ->getResult();
    }
}