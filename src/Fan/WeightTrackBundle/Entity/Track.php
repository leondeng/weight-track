<?php
namespace Fan\WeightTrackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Track
 *
 * @ORM\Table(name="wt_track", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="wt_track_user_date_idx", columns={"user_id", "date"})
 * })
 * @ORM\Entity
 *
 * @ExclusionPolicy("all")
 */
class Track
{
  /**
   *
   * @var integer
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   *
   * @Expose
   */
  private $id;

  /**
   * @var integer
   * @ORM\Column(name="weight", type="decimal", precision=5, scale=2)
   * @Assert\Type(
   *   type="numeric",
   *   message="Invalid weight!"
   * )
   * @Assert\NotBlank
   *
   * @Expose
   */
  private $weight;

  /**
   * @var Date
   * @ORM\Column(name="date", type="date")
   * @Assert\Date(
   *   message="Invalid date!"
   * )
   * @Assert\NotBlank
   *
   * @Expose
   */
  private $date;

  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="tracks")
   */
  private $user;

  public function getId() {
    return $this->id;
  }

  public function setWeight($weight) {
    $this->weight = $weight;
    return $this;
  }

  public function getWeight() {
    return $this->weight;
  }

  public function setDate($date) {
    $this->date = $date;
    return $this;
  }

  public function getDate() {
    return $this->date;
  }

  public function setUser(User $user) {
    $this->user = $user;
    return $this;
  }

  public function getUser() {
    return $this->user;
  }
}