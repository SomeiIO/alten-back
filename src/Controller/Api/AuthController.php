<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    // --------------------------
    // SIGN UP
    // --------------------------
    #[Route('/account', name: 'signup', methods: ['POST'])]
    #[OA\Post(
        path: '/api/account',
        summary: 'Create a new user account',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'name'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'myPass123'),
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created successfully'
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
        ]
    )]
    public function signup(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $required = ['email', 'password', 'name'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return $this->json(["error" => "$field is required"], 400);
            }
        }

        $existing = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existing) {
            return $this->json(['error' => 'Email already used'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setAddress($data['address'] ?? null);
        $user->setPhone($data['phone'] ?? null);
        $user->setPassword($this->hasher->hashPassword($user, $data['password']));

        $em->persist($user);
        $em->flush();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
        ], Response::HTTP_CREATED);
    }

    // --------------------------
    // SIGN IN
    // --------------------------
    #[Route('/token', name: 'signin', methods: ['POST'])]
    #[OA\Post(
        path: '/api/token',
        summary: 'Authenticate a user and return a JWT token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'admin@admin.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'admin'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'JWT token returned',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials')
        ]
    )]
    public function signin(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return $this->json(['error' => 'email and password required'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (!$user) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        if (!$this->hasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $this->jwtManager->create($user);

        return $this->json([
            'token' => $token
        ]);
    }
}
