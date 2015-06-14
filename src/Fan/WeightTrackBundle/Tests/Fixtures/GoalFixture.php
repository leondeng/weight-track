<?php

namespace Fan\WeightTrackBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Fan\WeightTrackBundle\Entity\Goal;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class GoalFixture extends AbstractFixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager) {
    $goal = new Goal();
    $goal->setGoal(100);

    $user = $this->getReference('user');
    $goal->setUser($user);

    $manager->persist($goal);
    $this->addReference('goal', $goal);
    $manager->flush();
  }

  public function getDependencies() {
    return array(
      'Fan\WeightTrackBundle\Tests\Fixtures\UserFixture',
     );
  }
}
