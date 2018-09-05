<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $commet = new Comment();
        $commet->setComments("Symfony developers (all levels of experience and 
        knowledge welcome)")
            ->setUser($manager->merge($this->getReference('user5')))
            ->setPost($manager->merge($this->getReference('post1')));
        $manager->persist($commet);

        $commet = new Comment();
        $commet->setComments("To learn about Symfony 4, Symfony Flex, and how it will 
        positively impact your everyday life")
            ->setUser($manager->merge($this->getReference('user4')))
            ->setPost($manager->merge($this->getReference('post1')));
        $manager->persist($commet);

        $commet = new Comment();
        $commet->setComments("PHP developers eager to widen their knowledge on 
        frameworks,best practice and quality PHP development techniques")
            ->setUser($manager->merge($this->getReference('user1')))
            ->setPost($manager->merge($this->getReference('post1')));
        $manager->persist($commet);

        $commet = new Comment();
        $commet->setComments("Drupal developers looking to learn more about 
        Symfony and its components")
            ->setUser($manager->merge($this->getReference('user4')))
            ->setPost($manager->merge($this->getReference('post4')));
        $manager->persist($commet);

        $commet = new Comment();
        $commet->setComments("Development managers, system administrators and
         other IT professionals with an interest in learning more about Symfony,
            getting to know the community and sharing ideas.")
            ->setUser($manager->merge($this->getReference('user2')))
            ->setPost($manager->merge($this->getReference('post7')));
        $manager->persist($commet);

        $commet = new Comment();
        $commet->setComments("We look forward to seeing you in September for
         the biggest UK Symfony conference.")
            ->setUser($manager->merge($this->getReference('user6')))
            ->setPost($manager->merge($this->getReference('post6')));
        $manager->persist($commet);

        $manager->flush();
    }

    public function getOrder()
    {
        return 50;
    }
}
