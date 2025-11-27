<?php

namespace App\Tests\Controller;

use App\Tests\Helper\DatabaseTestCase;
use App\Tests\Helper\AuthTestTrait;
use App\Enum\InventoryStatus;

class ProductControllerTest extends DatabaseTestCase
{
    use AuthTestTrait;

    public function testListProductsInitiallyEmpty(): void
    {
        $token = $this->loginAsUser($this->client);

        $this->client->request(
            'GET',
            '/api/products',
            [],
            [],
            ['HTTP_Authorization' => "Bearer $token"]
        );

        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertIsArray($data);
        $this->assertCount(0, $data, "Products list should be empty at start.");
    }

    public function testAdminCanCreateProduct(): void
    {
        $token = $this->loginAsAdmin($this->client);

        $payload = [
            "code" => "PRDT001",
            "name" => "Test Product",
            "description" => "A great test product.",
            "price" => 12.99,
            "inventoryStatus" => InventoryStatus::INSTOCK
        ];

        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => "Bearer $token"
            ],
            json_encode($payload)
        );

        $response = $this->client->getResponse();
        $this->assertSame(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey("id", $data);
        $this->assertSame("Test Product", $data["name"]);
        $this->assertSame(12.99, $data["price"]);
    }

    public function testUserCannotCreateProduct(): void
    {
        $token = $this->loginAsUser($this->client);

        $payload = [
            "code" => "PRDT001",
            "name" => "Test Product",
            "description" => "A great test product.",
            "price" => 12.99,
            "inventoryStatus" => InventoryStatus::INSTOCK
        ];

        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => "Bearer $token"
            ],
            json_encode($payload)
        );

        $response = $this->client->getResponse();

        $this->assertSame(403, $response->getStatusCode(), "Users should not be allowed to create products.");
    }

    public function testAdminCanUpdateProduct(): void
    {
        $token = $this->loginAsAdmin($this->client);

        // 1. Create product
        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => "Bearer $token"
            ],
            json_encode([
                "code" => "PRDT001",
                "name" => "Test Product",
                "description" => "A great test product.",
                "price" => 12.99,
                "inventoryStatus" => InventoryStatus::INSTOCK
            ])
        );

        $product = json_decode($this->client->getResponse()->getContent(), true);
        $productId = $product["id"];

        // 2. Update
        $this->client->request(
            'PUT',
            '/api/products/' . $productId,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => "Bearer $token"
            ],
            json_encode([
                "code" => "PRDT001",
                "name" => "Updataded Test Product",
                "description" => "A great test product.",
                "price" => 10.99,
                "inventoryStatus" => InventoryStatus::OUTOFSTOCK
            ])
        );

        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $updated = json_decode($response->getContent(), true);

        $this->assertSame("Updataded Test Product", $updated["name"]);
        $this->assertSame(10.99, $updated["price"]);
    }

    public function testAdminCanDeleteProduct(): void
    {
        $token = $this->loginAsAdmin($this->client);

        // Create product
        $this->client->request(
            'POST',
            '/api/products',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => "Bearer $token"
            ],
            json_encode([
                "code" => "DEL001",
                "name" => "To Delete",
                "description" => "Delete me",
                "price" => 9.99,
                "inventoryStatus" => InventoryStatus::OUTOFSTOCK
            ])
        );

        $product = json_decode($this->client->getResponse()->getContent(), true);
        $productId = $product["id"];

        // Delete
        $this->client->request(
            'DELETE',
            '/api/products/' . $productId,
            [],
            [],
            ['HTTP_Authorization' => "Bearer $token"]
        );

        $this->assertSame(204, $this->client->getResponse()->getStatusCode());

        // Confirm deletion
        $this->client->request(
            'GET',
            '/api/products/' . $productId,
            [],
            [],
            ['HTTP_Authorization' => "Bearer $token"]
        );

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}
