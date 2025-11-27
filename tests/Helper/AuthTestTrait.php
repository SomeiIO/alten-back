<?php

namespace App\Tests\Helper;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

trait AuthTestTrait
{
    protected function loginAs(KernelBrowser $client, string $email, string $password): string
    {
        $client->request(
            'POST',
            '/api/token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => $email, 'password' => $password])
        );

        $response = $client->getResponse();
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \RuntimeException("Login failed: " . $response->getContent());
        }

        $data = json_decode($response->getContent(), true);

        if (!isset($data['token'])) {
            throw new \RuntimeException("Token missing: " . $response->getContent());
        }

        return $data['token'];
    }

    protected function loginAsAdmin(KernelBrowser $client): string
    {
        return $this->loginAs($client, "admin@admin.com", "admin");
    }

    protected function loginAsUser(KernelBrowser $client): string
    {
        return $this->loginAs($client, "user@test.com", "password");
    }
}
