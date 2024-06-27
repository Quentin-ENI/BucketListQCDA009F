<?php

namespace App\Repository;

use App\Entity\Wish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wish>
 */
class WishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wish::class);
    }

    public function findWishesAndCategory(): array {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT w.id, w.title, c.name AS categoryName FROM wish AS w INNER JOIN category AS c ON w.category_id = c.id";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();


        return $resultSet->fetchAllAssociative();
    }
}
