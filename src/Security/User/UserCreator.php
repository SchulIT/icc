<?php

namespace App\Security\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;
use LightSaml\SpBundle\Security\User\UsernameMapperInterface;

class UserCreator implements UserCreatorInterface {

    private $em;
    private $userMapper;
    private $usernameMapper;

    public function __construct(EntityManagerInterface $em, UserMapper $userMapper, UsernameMapperInterface $usernameMapper) {
        $this->em = $em;
        $this->userMapper = $userMapper;
        $this->usernameMapper = $usernameMapper;
    }

    /**
     * @inheritDoc
     */
    public function createUser(Response $response) {
        $user = (new User())
            ->setUsername($this->usernameMapper->getUsername($response));

        $this->userMapper->mapUser($user, $response);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}