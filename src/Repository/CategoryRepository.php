<?php

namespace App\Repository;

use App\Entity\Category;
use App\Utils\BookCountCacheManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    private $bookCountCacheManager;

    public function __construct(ManagerRegistry $registry, BookCountCacheManager $bookCountCacheManager)
    {
        parent::__construct($registry, Category::class);

        $this->bookCountCacheManager = $bookCountCacheManager;
    }

    public function getBooksCount()
    {
        $categories = $this->findAll();
        
        return $this->bookCountCacheManager->build($categories);
    }
}