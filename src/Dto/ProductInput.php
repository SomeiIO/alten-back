<?php

namespace App\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    schema: "ProductInput",
    description: "Payload for creating or updating a product"
)]
class ProductInput
{
    #[Assert\NotBlank]
    #[OA\Property(example: "PRD001")]
    public string $code;

    #[Assert\NotBlank]
    #[OA\Property(example: "Red T-Shirt")]
    public string $name;

    #[Assert\NotBlank]
    #[OA\Property(example: "Comfortable cotton t-shirt")]
    public string $description;

    #[Assert\NotBlank]
    #[OA\Property(example: "https://example.com/tshirt.jpg")]
    public string $image;

    #[Assert\NotBlank]
    #[OA\Property(example: "Clothing")]
    public string $category;

    #[Assert\NotNull]
    #[OA\Property(example: 19.99)]
    public float $price;

    #[Assert\NotNull]
    #[OA\Property(example: 20)]
    public int $quantity;

    #[Assert\NotBlank]
    #[OA\Property(example: "INT-REF-12")]
    public string $internalReference;

    #[Assert\NotNull]
    #[OA\Property(example: 2)]
    public int $shellId;

    #[Assert\NotBlank]
    #[OA\Property(example: "INSTOCK")]
    public string $inventoryStatus;
}
