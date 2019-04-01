<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\MessageVisibility;
use App\Entity\UserType;
use PHPUnit\Framework\TestCase;

class MessageVisibilityTest extends TestCase {
    public function testGettersSetters() {
        $visibility = new MessageVisibility();

        $message = new Message();
        $visibility->setMessage($message);
        $this->assertEquals($message, $visibility->getMessage());

        $type = UserType::Intern();
        $visibility->setUserType($type);
        $this->assertEquals($type, $visibility->getUserType());
    }
}