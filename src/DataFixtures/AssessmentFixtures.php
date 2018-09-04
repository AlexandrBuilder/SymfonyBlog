<?php

namespace App\DataFixtures;

use App\Entity\Assessment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AssessmentFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $assessment1 = new Assessment();
        $assessment1->setPossitiveAssessment()
            ->setPost($manager->merge($this->getReference('post1')))
            ->setUser($manager->merge($this->getReference('user3')));
        $manager->persist($assessment1);

        $assessment2 = new Assessment();
        $assessment2->setPossitiveAssessment()
            ->setPost($manager->merge($this->getReference('post1')))
            ->setUser($manager->merge($this->getReference('user4')));
        $manager->persist($assessment2);

        $assessment3 = new Assessment();
        $assessment3->setPossitiveAssessment()
            ->setPost($manager->merge($this->getReference('post1')))
            ->setUser($manager->merge($this->getReference('user5')));
        $manager->persist($assessment3);

        $assessment4 = new Assessment();
        $assessment4->setPossitiveAssessment()
            ->setPost($manager->merge($this->getReference('post1')))
            ->setUser($manager->merge($this->getReference('user6')));
        $manager->persist($assessment4);

        $assessment5 = new Assessment();
        $assessment5->setNegativeAssessment()
            ->setPost($manager->merge($this->getReference('post4')))
            ->setUser($manager->merge($this->getReference('user1')));
        $manager->persist($assessment5);

        $assessment6 = new Assessment();
        $assessment6->setNegativeAssessment()
            ->setPost($manager->merge($this->getReference('post4')))
            ->setUser($manager->merge($this->getReference('user2')));
        $manager->persist($assessment6);

        $assessment7 = new Assessment();
        $assessment7->setNegativeAssessment()
            ->setPost($manager->merge($this->getReference('post4')))
            ->setUser($manager->merge($this->getReference('user5')));
        $manager->persist($assessment7);

        $assessment8 = new Assessment();
        $assessment8->setPossitiveAssessment()
            ->setPost($manager->merge($this->getReference('post4')))
            ->setUser($manager->merge($this->getReference('user6')));
        $manager->persist($assessment8);

        $assessment9 = new Assessment();
        $assessment9->setPossitiveAssessment()
            ->setPost($manager->merge($this->getReference('post6')))
            ->setUser($manager->merge($this->getReference('user4')));
        $manager->persist($assessment9);

        $manager->flush();
    }

    public function getOrder()
    {
        return 40;
    }
}
