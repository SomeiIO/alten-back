<?php

namespace App\Tests\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Tools\SchemaTool;
use App\Entity\User;

abstract class DatabaseTestCase extends WebTestCase
{
    protected EntityManagerInterface|null $em;
    protected UserPasswordHasherInterface $passwordHasher;

    protected $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $this->em = $container->get(EntityManagerInterface::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $this->resetDatabase();
        $this->loadBaseFixtures();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->em->close();
        $this->em = null;
    }

    protected function resetDatabase(): void
    {
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->em);
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);
    }

    protected function loadBaseFixtures(): void
    {
        // ADMIN user
        $admin = new User();
        $admin->setEmail("admin@admin.com");
        $admin->setName("admin");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, "admin"));
        $this->em->persist($admin);

        // NORMAL user
        $user = new User();
        $user->setEmail("user@test.com");
        $user->setRoles(["ROLE_USER"]);
        $user->setName("user");
        $user->setPassword($this->passwordHasher->hashPassword($user, "password"));
        $this->em->persist($user);

        $this->em->flush();
    }

    protected function getUserFromEmail(string $email): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \RuntimeException("Test user not found: $email");
        }

        return $user;
    }
}
