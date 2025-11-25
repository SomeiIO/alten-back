<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\ShoppingBag;
use App\Entity\Product;
use App\Repository\ShoppingBagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/bag', name: 'api_bag_')]
class ShoppingBagController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ShoppingBagRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $items = $repo->findBy(['user' => $user]);

        return $this->json($items, 200, [], [
            'groups' => ['bag:read', 'product:read']
        ]);
    }

    #[Route('/add/{productId}', name: 'add', methods: ['POST'])]
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

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        ?ShoppingBag $bagItem,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse
    {
        if (!$bagItem) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $bagItem->setQuantity($data['quantity']);

        $em->flush();

        return $this->json($bagItem, 200, [], [
            'groups' => ['bag:read']
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
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
