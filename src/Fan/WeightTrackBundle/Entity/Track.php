<?php
namespace Fan\WeightTrackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Track
 *
 * @ORM\Table(name="wt_track")
 * @ORM\Entity
 */
class Track
{

  use \Fan\WeightTrackBundle\Traits\Accessor;

  /**
   *
   * @var integer
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
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
   */
  private $weight;

  /**
   * @var Date
   * @ORM\Column(name="date", type="date")
   * @Assert\Date(
   *   message="Invalid date!"
   * )
   * @Assert\NotBlank
   */
  private $date;

  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="tracks")
   */
  private $user;
}