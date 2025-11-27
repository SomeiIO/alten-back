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

    #[OA\Property(example: "Comfortable cotton t-shirt")]
    public string $description;

    #[OA\Property(example: "https://example.com/tshirt.jpg")]
    public string $image;

    #[OA\Property(example: "Clothing")]
    public string $category;

    #[OA\Property(example: 19.99)]
    public float $price;

    #[OA\Property(example: 20)]
    public int $quantity;

    #[OA\Property(example: "INT-REF-12")]
    public string $internalReference;

    #[OA\Property(example: 2)]
    public int $shellId;

    #[Assert\NotBlank]
    #[OA\Property(example: "INSTOCK")]
    public string $inventoryStatus;
}
