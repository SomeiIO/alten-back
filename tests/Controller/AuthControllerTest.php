<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testSignup(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/account', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'new@user.com',
            'password' => 'pass123',
            'name' => 'New User'
        ]));

        $this->assertResponseStatusCodeSame(201);
    }

    public function testLogin(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/token', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'admin@admin.com',
            'password' => 'admin'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', json_decode($client->getResponse()->getContent(), true));
    }
}
