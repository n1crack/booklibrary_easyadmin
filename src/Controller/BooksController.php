<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Service\BookImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BooksController extends AbstractController
{
  private $bookRepository;
  private $categoryRepository;

  public function __construct(BookRepository $bookRepository, CategoryRepository $categoryRepository)
  {
    $this->bookRepository = $bookRepository;
    $this->categoryRepository = $categoryRepository;
  }

  /**
   * @Route("/books/create", name="book_create", methods={"GET","POST"})
   */
  public function create(Request $request, EntityManagerInterface $em, BookImageUploader $bookImageUploader, CacheItemPoolInterface $cache)
  {
    $form = $this->createForm(BookType::class, new Book());
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      /** @var Book $book */
      $book = $form->getData();
      /** @var UploadedFile $uploadedFile */
      $uploadedFile = $form['image']->getData();

      $uploadedImageName = $bookImageUploader->upload($uploadedFile);
      $book->setImage($uploadedImageName);

      $em->persist($book);
      $em->flush();

      return $this->redirectToRoute('index');
    }

    $cache->deleteItem('books_count');

    return $this->render('books/create.html.twig', [
      'bookForm' => $form->createView()
    ]);
  }

  /**
   * @Route("/", name="index", methods="GET")
   * @Route("/search/{category}", name="search", methods="GET")
   */
  public function search(Request $request)
  {
    $search = $request->get('q');
    $page = $request->query->getInt('page') == null ? 1 : $request->query->getInt('page');

    $category_id = $request->query->get('category');
    $category = $category_id ? $this->categoryRepository->find($category_id) : null;

    $categories = $this->categoryRepository->findAll();

    $books = $this->bookRepository->findBySearch($category, $search, $page);

    return $this->render('books/index.html.twig', [
      'categories' => $categories,
      'category' => $category,
      'book_count_by_categories' => $this->categoryRepository->getBooksCount(),
      'books' => $books['data'],
      'pagination' => $books['pagination']
    ]);
  }
}
