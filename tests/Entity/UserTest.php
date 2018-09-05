<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testNoVerifiedStatusUser()
    {
        $user = new User();
        $user->setName("Vasia Kotolov")
            ->setEmail("vasia@mail.ru")
            ->setPassword(password_hash("12345678", PASSWORD_DEFAULT));

        $this->assertEquals(User::CONST_NOT_VERIFIED, $user->getStatus());
    }

    public function testActiveStatusUser()
    {
        $user = new User();
        $user->setName("Vasia Kotolov")
            ->setEmail("vasia@mail.ru")
            ->activateUser()
            ->setPassword(password_hash("12345678", PASSWORD_DEFAULT));

        $this->assertEquals(User::CONST_ACTIVE, $user->getStatus());
    }

    public function testBlockedStatusUser()
    {
        $user = new User();
        $user->setName("Vasia Kotolov")
            ->setEmail("vasia@mail.ru")
            ->activateUser()
            ->setBlockStatusUser()
            ->setPassword(password_hash("12345678", PASSWORD_DEFAULT));

        $this->assertEquals(User::CONST_BLOCKED, $user->getStatus());
    }

}
