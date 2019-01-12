<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickEditControllerTest extends WebTestCase
{
    public function testindex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/trick/edit/commodi-rerum-earum');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}