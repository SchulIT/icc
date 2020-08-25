<?php

namespace App\Notification\WebPush;

use App\Entity\User;
use App\Entity\UserWebPushSubscription;
use App\Exception\UnexpectedTypeException;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionInterface;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserSubscriptionManager implements UserSubscriptionManagerInterface {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param UserInterface $user
     * @param string $subscriptionHash
     * @param array $subscription
     * @param array $options
     * @return UserSubscriptionInterface
     * @throws UnexpectedTypeException
     */
    public function factory(UserInterface $user, string $subscriptionHash, array $subscription, array $options = []): UserSubscriptionInterface {
        if(!$user instanceof User) {
            throw new UnexpectedTypeException($user, User::class);
        }

        return new UserWebPushSubscription($user, $subscriptionHash, $subscription);
    }

    /**
     * @inheritDoc
     */
    public function hash(string $endpoint, UserInterface $user): string {
        return hash('sha256', $endpoint);
    }

    /**
     * @inheritDoc
     */
    public function getUserSubscription(UserInterface $user, string $subscriptionHash): ?UserSubscriptionInterface {
        return $this->em->getRepository(UserWebPushSubscription::class)->findOneBy([
            'user' => $user,
            'subscriptionHash' => $subscriptionHash
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findByUser(UserInterface $user): iterable {
        return $this->em->getRepository(UserWebPushSubscription::class)->findBy([
            'user' => $user
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findByHash(string $subscriptionHash): iterable {
        return $this->em->getRepository(UserWebPushSubscription::class)->findBy([
            'subscriptionHash' => $subscriptionHash,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function save(UserSubscriptionInterface $userSubscription): void {
        $this->em->persist($userSubscription);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function delete(UserSubscriptionInterface $userSubscription): void {
        $this->em->remove($userSubscription);
        $this->em->flush();
    }
}