<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em) {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Season();

            $fixture->setNumber('Number ' . $i);
            $fixture->setPreserved('Preserved ' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setAlternativeTitle('AlternativeTitle ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setPodcast($this->getReference('podcast.1'));
            $fixture->setPublisher($this->getReference('publisher.1'));
            $em->persist($fixture);
            $this->setReference('season.' . $i, $fixture);
        }

        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies() {
        // add dependencies here, or remove this
        // function and "implements DependentFixtureInterface" above
        return [
            PodcastFixtures::class,
            PublisherFixtures::class,
        ];
    }

}
