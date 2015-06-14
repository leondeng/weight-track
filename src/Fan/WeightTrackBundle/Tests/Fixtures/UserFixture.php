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
    $user = $this->getUser();


    $manager->persist($user);
    $this->referenceRepository->addReference('user', $user);
    $manager->flush();
  }

  public function getUser() {
    return new User();
  }
}
