<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;

class UserRepository extends AbstractTransactionalRepository implements UserRepositoryInterface {

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
    public function findOneByUuid(string $uuid): ?User {
        return $this->em->getRepository(User::class)
            ->findOneBy([
                'uuid' => $uuid
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
    public function findAllParentsByStudents(array $students): array {
        $studentIds = array_map(fn(Student $student) => $student->getId(), $students);

        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('uInner.id')
            ->from(User::class, 'uInner')
            ->leftJoin('uInner.students', 'sInner')
            ->where($qb->expr()->in('sInner.id', ':students'))
            ->andWhere('uInner.userType = :type');

        $qb
            ->select(['u', 's'])
            ->from(User::class, 'u')
            ->leftJoin('u.students', 's')
            ->where($qb->expr()->in('u.id', $qbInner->getDQL()))
            ->setParameter('students', $studentIds)
            ->setParameter('type', UserType::Parent());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllStudentsByStudents(array $students): array {
        $studentIds = array_map(fn(Student $student) => $student->getId(), $students);

        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('uInner.id')
            ->from(User::class, 'uInner')
            ->leftJoin('uInner.students', 'sInner')
            ->where($qb->expr()->in('sInner.id', ':students'))
            ->andWhere('uInner.userType = :type');

        $qb
            ->select(['u', 's'])
            ->from(User::class, 'u')
            ->leftJoin('u.students', 's')
            ->where($qb->expr()->in('u.id', $qbInner->getDQL()))
            ->setParameter('students', $studentIds)
            ->setParameter('type', UserType::Student());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllTeachers(array $teachers): array {
        $teacherIds = array_map(fn(Teacher $teacher) => $teacher->getId(), $teachers);

        $qb = $this->em->createQueryBuilder();

        $qbInner = $this->em->createQueryBuilder()
            ->select('uInner.id')
            ->from(User::class, 'uInner')
            ->leftJoin('uInner.teacher', 'tInner')
            ->where($qb->expr()->in('tInner.id', ':teachers'))
            ->andWhere('uInner.userType = :type');

        $qb
            ->select(['u', 't'])
            ->from(User::class, 'u')
            ->leftJoin('u.teacher', 't')
            ->where($qb->expr()->in('u.id', $qbInner->getDQL()))
            ->setParameter('teachers', $teacherIds)
            ->setParameter('type', UserType::Teacher());

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
        return $this->em->getRepository(User::class)
            ->findBy([
                'isExamNotificationsEnabled' => true
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByNotifySubstitutions() {
        return $this->em->getRepository(User::class)
            ->findBy([
                'isSubstitutionNotificationsEnabled' => true
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByNotifyMessages(Message $message) {
        return $this->em->getRepository(User::class)
            ->findBy([
                'isMessageNotificationsEnabled' => true
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllByUserTypes(array $types): array {
        $typeNames = array_map(fn(UserType $type) => $type->getValue(), $types);

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

    /**
     * @inheritDoc
     */
    public function removeOrphaned(): int {
        $qbOrphaned = $this->em->createQueryBuilder()
            ->select('u.id')
            ->from(User::class, 'u')
            ->leftJoin('u.students', 's')
            ->leftJoin('u.teacher', 't')
            ->where('u.userType IN (:types)')
            ->andWhere('t.id IS NULL')
            ->andWhere('s.id IS NULL');

        $qb = $this->em->createQueryBuilder();

        return (int)$qb->delete(User::class, 'user')
            ->where(
                $qb->expr()->in('user.id', $qbOrphaned->getDQL())
            )
            ->setParameter('types', [ UserType::Teacher()->getValue(), UserType::Student()->getValue(), UserType::Parent()->getValue() ])
            ->getQuery()
            ->execute();
    }
}