<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{

  public const CACHE_EXPIRE_IN_SECONDS = 5 * 60; // 5 dk cache..

  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Category::class);
  }

  public function getBooksCount(CacheItemPoolInterface $cache)
  {
    $categories = $this->findAll();

    if (!$cache->hasItem('books_count')) {
      $temp = [];
      foreach ($categories as $category) {
        $temp[$category->getId()] = $category->getBooksCount();
      }
      $books_count = $cache->getItem('books_count')->set(json_encode($temp));

      $cache->save($books_count); 
    }

    return json_decode($cache->getItem('books_count')->get(), true);
  }
}
