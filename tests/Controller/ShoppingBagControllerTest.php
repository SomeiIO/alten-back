<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Entity\ShoppingBag;
use App\Tests\Helper\DatabaseTestCase;
use App\Tests\Helper\AuthTestTrait;

class ShoppingBagControllerTest extends DatabaseTestCase
{
    use AuthTestTrait;

    public function testListEmptyBag(): void
    {
        $token = $this->loginAsUser($this->client);

        $this->client->request('GET', '/api/bag', [], [], [
            'HTTP_Authorization' => "Bearer $token"
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertSame('[]', $this->client->getResponse()->getContent());
    }

    public function testAddProductToBag(): void
    {
        $token = $this->loginAsUser($this->client);

        // Create a product in DB
        $product = new Product();
        $product->setCode('PRD001')
                ->setName('Test')
                ->setDescription('x')
                ->setImage('x')
                ->setCategory('x')
                ->setPrice(10)
                ->setQuantity(5)
                ->setInternalReference('x')
                ->setShellId(1)
                ->setInventoryStatus(\App\Enum\InventoryStatus::INSTOCK)
                ->setRating(4);
        $this->em->persist($product);
        $this->em->flush();

        $this->client->request('POST', "/api/bag/add/{$product->getId()}", [], [], [
            'HTTP_Authorization' => "Bearer $token",
            'CONTENT_TYPE' => 'application/json'
        ], json_encode(['quantity' => 2]));

        $this->assertResponseStatusCodeSame(201);
    }

    public function testDeleteBagItem(): void
    {
        $token = $this->loginAsUser($this->client);

        // Setup product + bag item
        $product = new Product();
        $product->setCode('PRD002')
                ->setName('Test2')
                ->setDescription('x')
                ->setImage('x')
                ->setCategory('x')
                ->setPrice(10)
                ->setQuantity(5)
                ->setInternalReference('x')
                ->setShellId(1)
                ->setInventoryStatus(\App\Enum\InventoryStatus::INSTOCK)
                ->setRating(4);

        $this->em->persist($product);

        $bagItem = new ShoppingBag();
        $bagItem->setUser($this->getUserFromEmail('user@test.com'));
        $bagItem->setProduct($product);
        $bagItem->setQuantity(1);

        $this->em->persist($bagItem);
        $this->em->flush();

        $this->client->request('DELETE', "/api/bag/{$bagItem->getId()}", [], [], [
            'HTTP_Authorization' => "Bearer $token"
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteProductFromBag(): void
    {
        $token = $this->loginAsUser($this->client);

        // Setup product + bag item
        $product = new Product();
        $product->setCode('PRD003')
                ->setName('Test3')
                ->setDescription('x')
                ->setImage('x')
                ->setCategory('x')
                ->setPrice(10)
                ->setQuantity(5)
                ->setInternalReference('x')
                ->setShellId(1)
                ->setInventoryStatus(\App\Enum\InventoryStatus::INSTOCK)
                ->setRating(4);

        $this->em->persist($product);

        $bagItem = new ShoppingBag();
        $bagItem->setUser($this->getUserFromEmail('user@test.com'));
        $bagItem->setProduct($product);
        $bagItem->setQuantity(1);

        $this->em->persist($bagItem);
        $this->em->flush();

        $this->client->request('DELETE', "/api/bag/product/{$product->getId()}", [], [], [
            'HTTP_Authorization' => "Bearer $token"
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
}
