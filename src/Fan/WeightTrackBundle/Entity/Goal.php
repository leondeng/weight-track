<?php
namespace Fan\WeightTrackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Goal
 *
 * @ORM\Table(name="wt_goal")
 * @ORM\Entity
 */
class Goal
{
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
   * @ORM\Column(name="goal", type="integer")
   * @Assert\Type(
   *   type="numeric",
   *   message="Invalid goal!"
   * )
   * @Assert\NotBlank
   */
  private $goal;

    /**
   * @ORM\OneToOne(targetEntity="User", inversedBy="goal")
   */
  private $user;
  
  public function getId() {
    return $this->id;
  }
  
  public function setGoal($goal) {
    $this->goal = $goal;
    return $this;
  }
  
  public function getGoal() {
    return $this->goal;
  }
  
  public function setUser(User $user) {
    $this->user = $user;
    return $this;
  }
  
  public function getUser() {
    return $this->user;
  }
}