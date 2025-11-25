<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    private function loginAsAdmin($client): string
    {
        $client->request('POST', '/api/token', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'admin@admin.com',
            'password' => 'admin'
        ]));

        return json_decode($client->getResponse()->getContent(), true)['token'];
    }

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

    public function testUserCannotCreateProduct(): void
    {
        $client = static::createClient();
        $token = $this->loginAsUser($client);

        $client->request('POST', '/api/products', [], [], [
            'HTTP_Authorization' => "Bearer $token",
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'code' => 'NEW1',
            'name' => 'Unauthorized Product'
        ]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminCanCreateProduct(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        $client->request('POST', '/api/products', [], [], [
            'HTTP_Authorization' => "Bearer $token",
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'code' => 'ADM1',
            'name' => 'Admin Product'
        ]));

        $this->assertResponseStatusCodeSame(201);
    }
}
