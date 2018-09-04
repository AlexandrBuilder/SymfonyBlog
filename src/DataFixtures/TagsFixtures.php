<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TagsFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tag1 = new Tag();
        $tag1->setName('Security');
        $manager->persist($tag1);

        $tag2 = new Tag();
        $tag2->setName('Generate');
        $manager->persist($tag2);

        $tag3 = new Tag();
        $tag3->setName('Url');
        $manager->persist($tag3);

        $tag4 = new Tag();
        $tag4->setName('Console');
        $manager->persist($tag4);

        $tag5 = new Tag();
        $tag5->setName('Configuring');
        $manager->persist($tag5);

        $tag6 = new Tag();
        $tag6->setName('Help');
        $manager->persist($tag6);

        $tag7 = new Tag();
        $tag7->setName('Symfony');
        $manager->persist($tag7);

        $tag8 = new Tag();
        $tag8->setName('Command');
        $manager->persist($tag8);

        $tag9 = new Tag();
        $tag9->setName('Globally');
        $manager->persist($tag9);

        $tag10 = new Tag();
        $tag10->setName('Community');
        $manager->persist($tag10);

        $manager->flush();

        $this->addReference('tag1', $tag1);
        $this->addReference('tag2', $tag2);
        $this->addReference('tag3', $tag3);
        $this->addReference('tag4', $tag4);
        $this->addReference('tag5', $tag5);
        $this->addReference('tag6', $tag6);
        $this->addReference('tag7', $tag7);
        $this->addReference('tag8', $tag8);
        $this->addReference('tag9', $tag9);
        $this->addReference('tag10', $tag10);

    }

    public function getOrder()
    {
        return 20;
    }
}
