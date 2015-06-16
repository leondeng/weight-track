<?php

namespace Fan\WeightTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontEndController extends Controller
{
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();
    $user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find(1); // hardcode user
    return $this->render('FanWeightTrackBundle:FrontEnd:index.html.twig', array(
      'user' => $user,
      'base_url' => $this->container->get( 'kernel' )->getEnvironment() == 'dev' ? '/app_dev.php/' : '/'
    ));
  }
}
