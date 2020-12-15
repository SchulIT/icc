<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use LightSaml\SpBundle\Security\Authentication\Token\SamlSpToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class AbstractControllerTest extends WebTestCase {

    protected $client;

    /**
     * @see https://symfony.com/doc/4.4/testing/http_authentication.html
     *
     * @param User $user
     * @param KernelInterface $kernel
     */
    protected function login(User $user, KernelInterface $kernel) {
        $session = $kernel->getContainer()->get('session');
        $firewallName = 'secured';
        $firewallContext = $firewallName;

        $token = new SamlSpToken($user->getRoles(), $firewallName, [
            "name_id" => "admin@schulit.dev",
            "internal_id" => null,
            "services" => []
        ], $user);

        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $tokenStorage = $kernel->getContainer()->get('security.token_storage');
        $tokenStorage->setToken($token);

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}