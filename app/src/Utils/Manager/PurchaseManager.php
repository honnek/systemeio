<?php

namespace App\Utils\Manager;

use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;

readonly class PurchaseManager
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function save(Purchase $purchase): void
    {
        $this->entityManager->persist($purchase);
        $this->entityManager->flush();
    }
}