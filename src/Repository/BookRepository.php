<?php

namespace App\Repository;

use App\Entity\Book;
use App\Utils\BookPaginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Book find($id, $lockMode = null, $lockVersion = null)
 * @method null|Book findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
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
            ->setCategory($category)
        ;

        $this->manager->persist($newBook);
        $this->manager->flush();
    }

    /**
     * @param mixed $category
     * @param mixed $search
     *
     * @return Paginator
     */
    public function findBySearch($category, $search, int $page)
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
            ->setParameter('author', '%'.$search.'%')
        ;

        if (isset($category)) {
            $queryBuilder = $queryBuilder
                ->andWhere('b.category = :id')
                ->setParameter(':id', $category)
            ;
        }

        $queryBuilder = $queryBuilder->orderBy('b.id', 'ASC')
            ->setMaxResults(BookPaginator::PAGINATOR_PER_PAGE)
            ->setFirstResult(($page - 1) * BookPaginator::PAGINATOR_PER_PAGE)
            ->getQuery()
        ;

        $data = new Paginator($queryBuilder);
        $pagination = (new BookPaginator())->get($page, $data->count());

        return compact('data', 'pagination');
    }
}