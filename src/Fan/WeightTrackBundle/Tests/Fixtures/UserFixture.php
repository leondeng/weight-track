<?php

namespace Fan\WeightTrackBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\SharedFixtureInterface;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Fan\WeightTrackBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture implements SharedFixtureInterface
{
  private $referenceRepository;

  public function setReferenceRepository(ReferenceRepository $referenceRepository) {
    $this->referenceRepository = $referenceRepository;
  }

  public function load(ObjectManager $manager) {
    $user1 = new User();
    $user2 = new User();

    $manager->persist($user1);
    $manager->persist($user2);
    $this->referenceRepository->addReference('user', $user1);
    $manager->flush();
  }
}
