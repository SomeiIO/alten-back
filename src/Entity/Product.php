<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use App\Enum\InventoryStatus;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[OA\Schema(
    schema: 'Product',
    description: "A shop product"
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(example: 1)]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['product:read'])]
    #[OA\Property(example: "PRD001")]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read'])]
    #[OA\Property(example: "T-Shirt")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['product:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read'])]
    private ?string $image = null;

    #[ORM\Column(length: 100)]
    #[Groups(['product:read'])]
    private ?string $category = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $quantity = null;

    #[ORM\Column(length: 150)]
    #[Groups(['product:read'])]
    private ?string $internalReference = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $shellId = null;

    #[ORM\Column(type: 'string', enumType: InventoryStatus::class)]
    #[Groups(['product:read'])]
    private ?InventoryStatus $inventoryStatus = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $rating = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $createdAt = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
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

    public function getId(): ?int { return $this->id; }

    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(string $image): static { $this->image = $image; return $this; }

    public function getCategory(): ?string { return $this->category; }
    public function setCategory(string $category): static { $this->category = $category; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): static { $this->price = $price; return $this; }

    public function getQuantity(): ?int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getInternalReference(): ?string { return $this->internalReference; }
    public function setInternalReference(string $internalReference): static { $this->internalReference = $internalReference; return $this; }

    public function getShellId(): ?int { return $this->shellId; }
    public function setShellId(int $shellId): static { $this->shellId = $shellId; return $this; }

    public function getInventoryStatus(): ?InventoryStatus { return $this->inventoryStatus; }
    public function setInventoryStatus(InventoryStatus $status): static { $this->inventoryStatus = $status; return $this; }

    public function getRating(): ?int { return $this->rating; }
    public function setRating(int $rating): static { $this->rating = $rating; return $this; }

    public function getCreatedAt(): ?int { return $this->createdAt; }
    public function setCreatedAt(int $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?int { return $this->updatedAt; }
    public function setUpdatedAt(int $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

}
