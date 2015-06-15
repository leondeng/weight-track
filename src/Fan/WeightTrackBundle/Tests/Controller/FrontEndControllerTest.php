<?php

namespace Fan\WeightTrackBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontEndControllerTest extends WebTestCase
{
    public function testViewtrend()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user/{id}');
    }

    public function testSetgoal()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user/{id}/setGoal');
    }

    public function testWeighthistory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user/{id}/history');
    }

}
