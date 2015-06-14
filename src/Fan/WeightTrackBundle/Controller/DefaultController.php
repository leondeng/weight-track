<?php

namespace Fan\WeightTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FanWeightTrackBundle:Default:index.html.twig', array('name' => $name));
    }
}
