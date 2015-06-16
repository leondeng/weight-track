<?php

namespace Fan\WeightTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontEndController extends Controller
{
  public function indexAction($id)
  {$em = $this->getDoctrine()->getManager();
    if ($user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find($id)) {
      return $this->render('FanWeightTrackBundle:FrontEnd:index.html.twig', array(
        // ...
      ));
    } else {
      throw $this->createNotFoundException('The user does not exist');
    }
  }
}
