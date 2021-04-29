<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Predis\Client;


/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 5;
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Book::class);
        $this->manager = $manager;
    }

    public function saveBook($name, $author, $image, $category)
    {
        $newBook = new Book();

        $newBook
            ->setName($name)
            ->setAuthor($author)
            ->setCategory($category);

        $this->manager->persist($newBook);
        $this->manager->flush();
    }


    /**
    * @return Paginator Returns an array of Book objects
    */
    public function findBySearch($category, $search, int $page): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('b');
        $queryBuilder = $queryBuilder
        ->where(
                $queryBuilder->expr()->orX(
                  $queryBuilder->expr()->like('b.name', ':name'),
                  $queryBuilder->expr()->like('b.author', ':author'),
                )
              )
              ->setParameter('name', '%'.$search.'%')
              ->setParameter('author', '%'.$search.'%');
        
        if (isset($category)) {
          $queryBuilder = $queryBuilder
                  ->andWhere('b.category = :id')
                  ->setParameter(':id', $category);
        }

        $queryBuilder = $queryBuilder->orderBy('b.id', 'ASC')
              ->setMaxResults(self::PAGINATOR_PER_PAGE)
              ->setFirstResult(($page-1) * self::PAGINATOR_PER_PAGE)
              ->getQuery();
              
        return new Paginator($queryBuilder);
    }

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
