<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\ShoppingBag;
use App\Entity\Product;
use App\Repository\ShoppingBagRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/bag', name: 'api_bag_')]
#[OA\Tag(name: 'ShoppingBag')]
class ShoppingBagController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: "Get shopping bag items for current user",
        responses: [
            new OA\Response(
                response: 200,
                description: "Returns all bag items for the authenticated user",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/ShoppingBag')
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function list(ShoppingBagRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $items = $repo->findBy(['user' => $user]);

        return $this->json($items, 200, [], [
            'groups' => ['bag:read', 'product:read']
        ]);
    }

    #[Route('/add/{productId}', name: 'add', methods: ['POST'])]
    #[OA\Post(
        summary: "Add product to shopping bag",
        parameters: [
            new OA\Parameter(
                name: "productId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer"),
                description: "ID of the product to add"
            )
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "quantity", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Product added to bag"),
            new OA\Response(response: 404, description: "Product not found"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function add(
        int $productId,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $product = $em->getRepository(Product::class)->find($productId);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $quantity = json_decode($request->getContent(), true)['quantity'] ?? 1;

        $bagItem = new ShoppingBag();
        $bagItem->setUser($user);
        $bagItem->setProduct($product);
        $bagItem->setQuantity($quantity);

        $em->persist($bagItem);
        $em->flush();

        return $this->json($bagItem, 201, [], [
            'groups' => ['bag:read', 'product:read']
        ]);
    }

    #[Route('/product/{productId}', name: 'delete_product', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Remove a product from the user's shopping bag",
        parameters: [
            new OA\Parameter(
                name: "productId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Product removed"),
            new OA\Response(response: 404, description: "Item not found"),
            new OA\Response(response: 401, description: "Unauthorized"),
        ]
    )]
    public function removeProductFromBag(
        int $productId,
        ShoppingBagRepository $bagRepo,
        EntityManagerInterface $em
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Retrieve the bag item for this user + product
        $bagItem = $bagRepo->findOneBy([
            'user' => $user,
            'product' => $productId,
        ]);

        if (!$bagItem) {
            return $this->json(['error' => 'Item not found in your bag'], 404);
        }

        $em->remove($bagItem);
        $em->flush();

        return $this->json(null, 204);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Delete a shopping bag item by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 204, description: "Item deleted"),
            new OA\Response(response: 404, description: "Item not found"),
            new OA\Response(response: 401, description: "Unauthorized"),
        ]
    )]
    public function delete(
        ?ShoppingBag $bagItem,
        EntityManagerInterface $em
    ): JsonResponse
    {
        if (!$bagItem) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $em->remove($bagItem);
        $em->flush();

        return $this->json(null, 204);
    }
}
