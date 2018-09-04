<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PostFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $post1 = new Post();
        $post1->setTitle('And if I win?')
            ->setDescription('To configure the Request Context - which is used by the URL Generator - 
            you can redefine the parameters it uses as default values to change the default host (localhost) and scheme (http). 
            You can also configure the base path (both for the URL generator and the assets) if Symfony is not running in the root directory.')
            ->addTag($manager->merge($this->getReference('tag1')))
            ->addTag($manager->merge($this->getReference('tag2')))
            ->setUser($manager->merge($this->getReference('user1')))
            ->published();
        $manager->persist($post1);

        $post2 = new Post();
        $post2->setTitle('Symfony Components')
            ->setDescription('The Components implement common features needed to develop websites. 
            They are the foundation of the Symfony full-stack framework, but they can also be used standalone even if you don\'t use the 
            framework as they don\'t have any mandatory dependencies. ')
            ->addTag($manager->merge($this->getReference('tag3')))
            ->addTag($manager->merge($this->getReference('tag4')))
            ->setUser($manager->merge($this->getReference('user1')))
            ->moderated();
        $manager->persist($post2);

        $post3 = new Post();
        $post3->setTitle('The Asset Component')
            ->setDescription('This practice is no longer recommended unless the web application is extremely simple. Hardcoding URLs can be a disadvantage because:
                Templates get verbose: you have to write the full path for each asset. When using the Asset component, you can group assets in packages to avoid repeating the common part of their path;
                Versioning is difficult: it has to be custom managed for each application. Adding a version (e.g. main.css?v=5) to the asset URLs is essential for some applications because it allows you to 
                control how the assets are cached. 
                The Asset component allows you to define different versioning strategies for each package;
                Moving assets location is cumbersome and error-prone: it requires you to carefully update the URLs of all assets included in all templates. 
                The Asset component allows to move assets effortlessly just by changing the base path value associated with the package of assets;
                It\'s nearly impossible to use multiple CDNs: this technique requires you to change the URL of the asset randomly for each request. 
                The Asset component provides out-of-the-box support for any number of multiple CDNs, both regular (http://) and secure (https://).
            ')
            ->addTag($manager->merge($this->getReference('tag6')))
            ->addTag($manager->merge($this->getReference('tag10')))
            ->setUser($manager->merge($this->getReference('user1')))
            ->declined();
        $manager->persist($post3);

        $post4 = new Post();
        $post4->setTitle('Diversity And Inclusion')
            ->setDescription('Symfony is committed to foster an open and welcoming environment for everyone.
                Read the Symfony Code of Conduct which governs all the official Symfony code repositories, activities and events.
                Meet the diverse team in charge of enforcing the Code of Conduct and review the reporting guidelines to notify us about Code of Conduct violations.
                Take part in the Symfony Diversity Initiative, a repository of ideas to improve diversity.
                Follow the diversity news and updates from the official Symfony blog.
            ')
            ->addTag($manager->merge($this->getReference('tag1')))
            ->addTag($manager->merge($this->getReference('tag3')))
            ->setUser($manager->merge($this->getReference('user4')))
            ->published();
        $manager->persist($post4);

        $post5 = new Post();
        $post5->setTitle('Pre-conference workshops at SymfonyLive London 2018 are almost sold out!')
            ->setDescription('All Symfony conferences come with pre-conference workshops. 
                We organized them to enable the conference attendees to get trained on Symfony and its ecosystem just before the conference, 
                during special workshops sessions at a very special price. The idea is to get the most out of the conference and enhance your 
                Symfony skills before learning new tips and tricks from the speakers’ experience during the conference. We organize 4 different 
                pre-conference workshops at SymfonyLive London 2018 on September 27th and they are almost all sold out!
                If you are thinking on registering for a workshop, don’t wait any longer and book your combo ticket (workshop and conference) before there are no seats left anymore. 
                Regular registration is open until September 3rd and the combo ticket is as low as £655! The workshop value at regular rate is £650. 
                Benefit from this great deal until September 3rd, after that date, the combo ticket will increase to £767.')
            ->addTag($manager->merge($this->getReference('tag5')))
            ->addTag($manager->merge($this->getReference('tag8')))
            ->setUser($manager->merge($this->getReference('user4')));
        $manager->persist($post5);

        $post6 = new Post();
        $post6->setTitle('CARE team training ')
            ->setDescription('Following the announcement of the CARE team members by Fabien, we would now move forward to organize a training to 
                ensure that we are well prepared to handle incident reports. For this training we intend to draw upon the expertise of Sage Sharp from Otter Tech. 
                A seats costs $350. We are looking for donations of one or more seats so that we can finance at least 5 seats. ')
            ->addTag($manager->merge($this->getReference('tag3')))
            ->addTag($manager->merge($this->getReference('tag7')))
            ->setUser($manager->merge($this->getReference('user2')))
            ->published();
        $manager->persist($post6);

        $post7 = new Post();
        $post7->setTitle('The New Symfony Documentation Search Engine')
            ->setDescription('Symfony boasts one of the largest documentation pools ever written for an Open-Source project. 
                Considering the ten different Symfony versions (from 2.0 to master) and including the code samples, Symfony Documentation has around 3.6 million words, 
                more than three times the word count of the entire Harry Potter series.
                It\'s hard to create a search engine¶
                This massive documentation requires providing an effective way to look for what you are interested in. At first we used Apache Solr to create a custom search engine, 
                but a few months ago we tried to improve it by switching to ElasticSearch.
                Given the complexity of creating a good search engine, before completing the ElasticSearch integration, we started looking at other alternatives. 
                The search engine as a service market is not very crowded, so it didn\'t take us long to review all the possibilities.
                Following this initial analysis, we chose Algolia as the most promising candidate and started developing a proof of concept for the new Symfony Documentation search engine.')
            ->addTag($manager->merge($this->getReference('tag2')))
            ->addTag($manager->merge($this->getReference('tag8')))
            ->setUser($manager->merge($this->getReference('user5')))
            ->published();
        $manager->persist($post7);

        $manager->flush();

        $this->addReference('post1', $post1);
        $this->addReference('post2', $post2);
        $this->addReference('post3', $post3);
        $this->addReference('post4', $post4);
        $this->addReference('post5', $post5);
        $this->addReference('post6', $post6);
        $this->addReference('post7', $post7);
    }

    public function getOrder()
    {
        return 30;
    }
}
