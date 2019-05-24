<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SettingsControllerTest extends WebTestCase
{
    public function testIndexmach()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/mach/index');
    }

    public function testAddmach()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/mach/add');
    }

}
