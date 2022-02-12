<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractControllerTest extends WebTestCase {

    protected KernelBrowser $client;

    /**
     * @see https://symfony.com/doc/4.4/testing/http_authentication.html
     *
     * @param User $user
     * @param KernelInterface $kernel
     */
    protected function login(User $user, KernelInterface $kernel) {
        $this->client->loginUser($user, 'secured');
    }
}