<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountControllerTest extends WebTestCase
{
    public function testlogin()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testregistration()
    {
        $client = static::createClient();

        $client->request('GET', '/registration');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testpasswordUpdate()
    {
        $client = static::createClient();

        $client->request('GET', '/account/password-update');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testprofile()
    {
        $client = static::createClient();

        $client->request('GET', '/account/profile');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}