<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserEntity(): void
    {
        $user = new User();
        $user->setEmail("test@test.com");
        $user->setName("Tester");
        $user->setPassword("hashed");

        $this->assertEquals("test@test.com", $user->getEmail());
        $this->assertEquals("Tester", $user->getName());
        $this->assertEquals("hashed", $user->getPassword());
    }
}
