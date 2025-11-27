<?php

namespace App\Tests\Controller;

use App\Tests\Helper\AuthTestTrait;
use App\Tests\Helper\DatabaseTestCase;

class AuthControllerTest extends DatabaseTestCase
{
    use AuthTestTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testLogin(): void
    {
        $token = $this->loginAsUser($this->client);
        $this->assertNotEmpty($token);
    }

    public function testSignup(): void
    {
        $this->client->request('POST', '/api/account', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'new@user.com',
            'password' => 'pass123',
            'name' => 'New User'
        ]));

        $this->assertResponseStatusCodeSame(201);
    }
}
