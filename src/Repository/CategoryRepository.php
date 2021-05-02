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

  private $cache;

  public function __construct(ManagerRegistry $registry, CacheItemPoolInterface $cache)
  {
    parent::__construct($registry, Category::class);

    $this->cache = $cache;
  }

  public function getBooksCount()
  {
    $categories = $this->findAll();

    if (!$this->cache->hasItem('books_count')) {
      $temp = [];
      foreach ($categories as $category) {
        $temp[$category->getId()] = $category->getBooksCount();
      }
      $books_count = $this->cache->getItem('books_count')->set(json_encode($temp));

      $this->cache->save($books_count); 
    }

    return json_decode($this->cache->getItem('books_count')->get(), true);
  }
}
