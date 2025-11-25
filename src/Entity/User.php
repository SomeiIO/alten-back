<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'bag:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'bag:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read', 'bag:read'])]
    private ?string $name = null;

    #[ORM\Column]
    private ?string $password = null;
    
    #[ORM\Column(nullable: true)]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?int $createdAt = null;

    #[ORM\Column]
    private ?int $updatedAt = null;

    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $now = time();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = time();
    }

    // Required by UserInterface
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function setRoles(array $roles): static
    {
        // No role
        return $this;
    }

    public function eraseCredentials(): void
    {
        // No temp sensitive data stored
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): static { $this->address = $address; return $this;}
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): static { $this->phone = $phone; return $this; }
    public function getCreatedAt(): ?int { return $this->createdAt; }
    public function setCreatedAt(int $createdAt): static { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?int { return $this->updatedAt; }
    public function setUpdatedAt(int $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

}
