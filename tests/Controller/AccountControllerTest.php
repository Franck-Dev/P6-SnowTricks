<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountControllerTest extends WebTestCase
{
    public function testregistration()
    {
        $client = static::createClient();

        $client->request('GET', '/registration');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}