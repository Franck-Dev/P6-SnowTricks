<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    public function testdetails()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/trick/details/indy');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Should show the trick main image
        $this->assertSame(1, $crawler->filter('div#trickMainImage')->count());
    }
}