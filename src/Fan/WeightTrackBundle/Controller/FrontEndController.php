<?php

namespace Fan\WeightTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontEndController extends Controller
{
  public function viewTrendAction($id)
  {
      return $this->render('FanWeightTrackBundle:FrontEnd:viewTrend.html.twig', array(
          // ...
      ));
  }

  public function setGoalAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    if ($user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find($id)) {
      $goal = $user->getGoal();
      $form = $this->createFormBuilder($goal)
        ->add('goal', 'text')
        ->add('save', 'submit', array('label' => 'Create Goal'))
        ->getForm();
      
      return $this->render('FanWeightTrackBundle:FrontEnd:setGoal.html.twig', array(
        'form' => $form->createView(),
      ));
    } else {
      throw $this->createNotFoundException('The user does not exist');
    }
  }

  public function weightHistoryAction($id)
  {
      return $this->render('FanWeightTrackBundle:FrontEnd:weightHistory.html.twig', array(
          // ...
      ));
  }

}
