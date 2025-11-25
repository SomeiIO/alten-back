<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\ShoppingBag;
use App\Enum\InventoryStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
         // ----------------------------
        // ADMIN USER
        // ----------------------------
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setName('Admin User');
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $admin->setAddress("Admin Street 1");
        $admin->setPhone("0000000000");
        $manager->persist($admin);

        // ----------------------------
        // NORMAL USER
        // ----------------------------
        $user = new User();
        $user->setEmail('demo@shop.com');
        $user->setName('Demo User');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setAddress("123 Demo Road");
        $user->setPhone("0123456789");
        $manager->persist($user);

        // ----------------------------
        // PRODUCTS
        // ----------------------------
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setCode("P$i");
            $product->setName("Product $i");
            $product->setDescription("Description for product $i");
            $product->setImage("image$i.png");
            $product->setCategory("Category A");
            $product->setPrice(random_int(10, 100));
            $product->setQuantity(random_int(1, 20));
            $product->setInternalReference("REF$i");
            $product->setShellId($i);
            $product->setRating(random_int(1, 5));
            $product->setInventoryStatus(InventoryStatus::INSTOCK);

            $manager->persist($product);

            // Add 1 product to demo user's shopping bag
            if ($i === 1) {
                $bag = new ShoppingBag();
                $bag->setUser($user);
                $bag->setProduct($product);
                $bag->setQuantity(2);
                $manager->persist($bag);
            }
        }

        $manager->flush();
    }
}
