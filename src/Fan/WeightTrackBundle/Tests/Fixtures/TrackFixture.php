<?php

namespace Fan\WeightTrackBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Fan\WeightTrackBundle\Entity\Track;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrackFixture extends AbstractFixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager) {
    $track = new Track();
    $track->setWeight(125);
    $track->setDate(date_create('2015-06-05'));

    $user = $this->getReference('user');
    $user->addTrack($track);

    $manager->persist($user);
    $manager->flush();
  }

  public function getDependencies() {
    return array(
      'Fan\WeightTrackBundle\Tests\Fixtures\UserFixture',
     );
  }
}
