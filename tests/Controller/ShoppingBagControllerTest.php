<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShoppingBagControllerTest extends WebTestCase
{
    private function loginAsUser($client): string
    {
        $client->request('POST', '/api/token', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'demo@shop.com',
            'password' => 'password'
        ]));

        return json_decode($client->getResponse()->getContent(), true)['token'];
    }

    public function testAddToBag(): void
    {
        $client = static::createClient();
        $token = $this->loginAsUser($client);

        $client->request('POST', '/api/bag/add/1', [], [], [
            'HTTP_Authorization' => "Bearer $token",
            'CONTENT_TYPE' => 'application/json'
        ], json_encode(['quantity' => 2]));

        $this->assertResponseStatusCodeSame(201);
    }
}
