<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setName("Vasia Kotolov")
            ->setEmail("vasia@mail.ru")
            ->activateUser()
            ->setPassword(password_hash("12345678", PASSWORD_DEFAULT));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setName("Petia Fufov")
            ->setEmail("petia@mail.ru")
            ->activateUser()
            ->setPassword(password_hash("123456789", PASSWORD_DEFAULT));
        $manager->persist($user2);

        $user3 = new User();
        $user3->setName("Alexander Kiselev")
            ->setEmail("alex@mail.ru")
            ->activateUser()
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(password_hash("1234567890", PASSWORD_DEFAULT));
        $manager->persist($user3);

        $user4 = new User();
        $user4->setName("Tihonov Retan")
            ->setEmail("tixa@mail.ru")
            ->activateUser()
            ->setPassword(password_hash("87654321", PASSWORD_DEFAULT));
        $manager->persist($user4);

        $user5 = new User();
        $user5->setName("Orew Terek")
            ->setEmail("orew@mail.ru")
            ->activateUser()
            ->setPassword(password_hash("qwertyui", PASSWORD_DEFAULT));
        $manager->persist($user5);

        $user6 = new User();
        $user6->setName("Dmitriy Tumov")
            ->setEmail("tumov@mail.ru")
            ->setPassword(password_hash("1234567890", PASSWORD_DEFAULT));
        $manager->persist($user6);

        $manager->flush();

        $this->addReference('user1', $user1);
        $this->addReference('user2', $user2);
        $this->addReference('user3', $user3);
        $this->addReference('user4', $user4);
        $this->addReference('user5', $user5);
        $this->addReference('user6', $user6);
    }

    public function getOrder()
    {
        return 10;
    }
}
