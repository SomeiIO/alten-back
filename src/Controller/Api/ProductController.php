<?php

namespace App\Controller\Api;

use App\Dto\ProductInput;
use App\Entity\Product;
use App\Enum\InventoryStatus;
use App\Repository\ProductRepository;
use App\Security\ProductVoter;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/products', name: 'api_products_')]
class ProductController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/products',
        summary: 'List all products',
        tags: ["Product"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Returns list of products",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Product")
                )
            )
        ]
    )]
    public function list(ProductRepository $repo): JsonResponse
    {
        return $this->json($repo->findAll(), 200, [], [
            'groups' => ['product:read']
        ]);
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get a single product by id',
        tags: ["Product"],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product found', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    public function detail(?Product $product): JsonResponse
    {
        if (!$product) {
            return $this->json(['error' => 'Not found'], 404);
        }

        return $this->json($product, 200, [], [
            'groups' => ['product:read']
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/products',
        summary: 'Create a product (admin only)',
        security: [['bearerAuth' => []]],
        tags: ["ProductAdmin"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted(ProductVoter::CREATE);
        /** @var ProductInput $data */
        $data = $serializer->deserialize($request->getContent(), ProductInput::class, 'json');

        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }
        $data = json_decode($request->getContent(), true);

        $product = new Product();

        $product
            ->setCode($data['code'])
            ->setName($data['name'])
            ->setDescription($data['description'] ?? null)
            ->setImage($data['image'] ?? null)
            ->setCategory($data['category'] ?? null)
            ->setPrice($data['price'] ?? 0)
            ->setQuantity($data['quantity'] ?? 0)
            ->setInternalReference($data['internalReference'] ?? null)
            ->setShellId($data['shellId'] ?? 0)
            ->setRating($data['rating'] ?? 0)
            ->setInventoryStatus(InventoryStatus::from($data['inventoryStatus']));

        $em->persist($product);
        $em->flush();

        return $this->json($product, 201, [], [
            'groups' => ['product:read']
        ]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Update a product (admin only)',
        security: [['bearerAuth' => []]],
        tags: ["ProductAdmin"],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Product updated'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function update(?Product $product, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted(ProductVoter::EDIT, $product);

        if (!$product) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $update = function (string $field, callable $setter) use ($data) {
            if (array_key_exists($field, $data) && $data[$field] !== null) {
                $setter($data[$field]);
            }
        };

        $update('code', fn($v) => $product->setCode($v));
        $update('name', fn($v) => $product->setName($v));
        $update('description', fn($v) => $product->setDescription($v));
        $update('image', fn($v) => $product->setImage($v));
        $update('category', fn($v) => $product->setCategory($v));
        $update('price', fn($v) => $product->setPrice($v));
        $update('quantity', fn($v) => $product->setQuantity($v));
        $update('internalReference', fn($v) => $product->setInternalReference($v));
        $update('shellId', fn($v) => $product->setShellId($v));
        $update('rating', fn($v) => $product->setRating($v));

        // Enum special case
        if (array_key_exists('inventoryStatus', $data) && $data['inventoryStatus'] !== null) {
            $product->setInventoryStatus(InventoryStatus::from($data['inventoryStatus']));
        }

        $em->flush();

        return $this->json($product, 200, [], [
            'groups' => ['product:read']
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Delete a product (admin only)',
        security: [['bearerAuth' => []]],
        tags: ["ProductAdmin"],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true)
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function delete(?Product $product, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted(ProductVoter::DELETE, $product);
        if (!$product) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(null, 204);
    }
}
