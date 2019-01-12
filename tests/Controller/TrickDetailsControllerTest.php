<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickDetailsControllerTest extends WebTestCase
{
    public function testindex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/trick/details/commodi-rerum-earum');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Should show the trick main image
        $this->assertSame(1, $crawler->filter('div#trickMainImage')->count());
    }
}