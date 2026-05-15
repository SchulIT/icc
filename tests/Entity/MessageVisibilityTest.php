<?php

namespace App\Tests\Entity;

use App\Common\Entity\UserType;
use App\Common\Entity\UserTypeEntity;
use PHPUnit\Framework\TestCase;

class MessageVisibilityTest extends TestCase {
    public function testGettersSetters() {
        $visibility = new UserTypeEntity();

        $type = UserType::Intern;
        $visibility->setUserType($type);
        $this->assertEquals($type, $visibility->getUserType());
    }
}