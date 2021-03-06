<?php

namespace Fan\WeightTrackBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Fan\WeightTrackBundle\Entity\Track;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrackFixture extends AbstractFixture implements DependentFixtureInterface
{
  const TRACKS = [
    '2015-06-03' => 132.52,
    '2015-06-04' => 130.15,
    '2015-06-05' => 125.46,
    '2015-06-06' => 126.33,
    '2015-06-07' => 125.70,
    '2015-06-08' => 125.54,
    '2015-06-09' => 124.18,
    '2015-06-10' => 124.34,
    '2015-06-11' => 123.25,
    '2015-06-12' => 121.10,
    '2015-06-13' => 122.84,
    '2015-06-14' => 120.93
  ];
  public function load(ObjectManager $manager) {
    $user = $this->getReference('user');

    foreach (self::TRACKS as $date => $weight) {
      $track = new Track();
      $track->setWeight($weight);
      $track->setDate(date_create($date));

      $user->addTrack($track);
    }
    $manager->persist($user);
    $manager->flush();
  }

  public function getDependencies() {
    return array(
      'Fan\WeightTrackBundle\Tests\Fixtures\UserFixture',
     );
  }
}
