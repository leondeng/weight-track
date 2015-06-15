<?php

namespace Fan\WeightTrackBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $request = $this->container->get('request');
        $id = $request->get('id');
        $menu->addChild('Home', array(
          'route' => 'homepage',
          'routeParameters' => array('id' => $id)
        ));

        $menu->addChild('Goal', array(
            'route' => 'set_goal',
            'routeParameters' => array('id' => $id)
        ));

        $menu->addChild('Weight History', array(
          'route' => 'weight_history',
          'routeParameters' => array('id' => $id)
        ));

        return $menu;
    }
}