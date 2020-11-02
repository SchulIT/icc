<?php

namespace App\Tests\Security\Voter;

use App\Entity\User;
use App\Entity\UserType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * These tests ensure that the ROLE_ADMIN role is not exposed to users.
 */
class LeakTest extends KernelTestCase {

    /**
     * @see https://symfony.com/doc/4.4/testing/http_authentication.html
     *
     * @param User $user
     * @param KernelInterface $kernel
     */
    private function login(User $user, KernelInterface $kernel) {
        $session = $kernel->getContainer()->get('session');
        $firewallName = 'secured';
        $firewallContext = $firewallName;

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $tokenStorage = $kernel->getContainer()->get('security.token_storage');
        $tokenStorage->setToken($token);
    }

    public function testUsersWithoutAdminRolesDoNotHaveAdminRoleExposed() {
        $types = UserType::values();

        foreach($types as $type) {
            $kernel = static::bootKernel();
            $user = (new User())
                ->setUserType($type);
            $user->setRoles(['ROLE_USER']);
            $this->login($user, $kernel);

            $authorizationChecker = $kernel->getContainer()->get('security.authorization_checker');
            $this->assertFalse($authorizationChecker->isGranted('ROLE_ADMIN'), sprintf('Ensure user of type %s does not have ROLE_ADMIN.', $type->getValue()));
        }
    }

}