<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\MessageConfirmation;
use App\Entity\MessageScope;
use App\Entity\User;
use App\Entity\UserType;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageConfirmationTest extends WebTestCase {
    public function testGettersSetters() {
        $confirmation = new MessageConfirmation();

        $message = new Message();
        $confirmation->setMessage($message);
        $this->assertEquals($message, $confirmation->getMessage());

        $user = new User();
        $confirmation->setUser($user);
        $this->assertEquals($user, $confirmation->getUser());
    }

    public function testUpdatedAt() {
        $kernel = static::createKernel();
        $kernel->boot();

        $em = $kernel->getContainer()->get('doctrine')
            ->getManager();

        $user = (new User())
            ->setIdpId(Uuid::fromString('1f1248d4-8742-4b89-a0c4-1f345ce5664a'))
            ->setFirstname('firstname')
            ->setLastname('lastname')
            ->setUsername('username')
            ->setEmail('username@school.it')
            ->setUserType(UserType::Teacher);

        $message = (new Message())
            ->setTitle('subject')
            ->setContent('content')
            ->setStartDate(new \DateTime('2019-01-01'))
            ->setExpireDate(new \DateTime('2019-01-03'))
            ->setScope(MessageScope::Appointments)
            ->setCreatedBy($user);

        $confirmation = (new MessageConfirmation())
            ->setMessage($message)
            ->setUser($user);

        $em->persist($message);
        $em->persist($user);
        $em->persist($confirmation);

        $em->flush();

        $this->assertNotNull($confirmation->getCreatedAt());

        $em->detach($message);

        /** @var Message $message */
        $message = $em->getRepository(Message::class)
            ->findOneBy([
                'id' => $message->getId()
            ]);

        $this->assertEquals(1, $message->getConfirmations()->count());
        $this->assertEquals($confirmation->getUser()->getId(), $message->getConfirmations()->first()->getUser()->getId());
    }
}