<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Promotion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $productA = (new Product())
            ->setSku('A')
            ->setName('A')
            ->setUnitPrice(50);

        $promoA = (new Promotion())
            ->setQuantity(3)
            ->setSpecialPrice(130)
            ->setProduct($productA);

        $productA->addPromotion($promoA);

        $manager->persist($productA);
        $manager->persist($promoA);

        $productB = (new Product())
            ->setSku('B')
            ->setName('B')
            ->setUnitPrice(30);

        $promoB = (new Promotion())
            ->setQuantity(2)
            ->setSpecialPrice(45)
            ->setProduct($productB);

        $productB->addPromotion($promoB);

        $manager->persist($productB);
        $manager->persist($promoB);

        $productC = (new Product())
            ->setSku('C')
            ->setName('C')
            ->setUnitPrice(20);

        $manager->persist($productC);

        $productD = (new Product())
            ->setSku('D')
            ->setName('D')
            ->setUnitPrice(10);

        $manager->persist($productD);

        $manager->flush();
    }
}
