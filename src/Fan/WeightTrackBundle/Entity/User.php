<?php
namespace Fan\WeightTrackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Doctrine\Common\Collections\Doctrine\Common\Collections;

/**
 * User
 *
 * @ORM\Table(name="wt_user")
 * @ORM\Entity
 */
class User
{

  use \Fan\WeightTrackBundle\Traits\Accessor;

  /**
   *
   * @var integer @ORM\Column(name="id", type="integer")
   *      @ORM\Id
   *      @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\OneToMany(targetEntity="Track", mappedBy="user", cascade={"persist"})
   * @ORM\OrderBy({"date" = "DESC"})
   */
  private $tracks;

  public function __construct() {
    $this->tracks = new ArrayCollection();
  }

  public function addTrack(Track $track) {
    $this->tracks[] = $track;
    $track->setUser($this);
    return $this;
  }

  public function removeTrack(Track $track) {
    $this->tracks->removeElement($track);
  }
}