<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

  #[Route('/books/create', name: 'books.store', methods: ["POST"])]
  public function create(Request $request, EntityManagerInterface $em)
  {

    $book = new Book();
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      /** @var Book $book */
      $book = $form->getData();

      /** @var UploadedFile $uploadedFile */
      $uploadedFile = $form['image']->getData();
      $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
      $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
      $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
      $uploadedFile->move(
        $destination,
        $newFilename
      );
      $book->setImage($newFilename);

      $em->persist($book);
      $em->flush();

      return $this->redirectToRoute('index');
    }
    return $this->render('books/create.html.twig', [
      'bookForm' => $form->createView()
    ]);
  }

  #[Route('/search/{category}', name: 'search', methods: ["GET"])]
  public function search(Request $request, Category $category = null)
  {
    $search = $request->get('q');
    $page = $request->query->getInt('page') == null ? 1 : $request->query->getInt('page');

    $categories = $this->categoryRepository->findAll();

    $books = $this->bookRepository->findBySearch($category, $search, $page);

    $category_id = ($category instanceof Category) ? $category->getId() : null;

    return $this->render('books/index.html.twig', [
      'categories' => $categories,
      'books' => $books,
      'book_count' => count($books),
      'book_count_by_categories' => $this->categoryRepository->getBooksCount(),
      'search_url' => $this->generateUrl('search', ['id' => $category_id, 'page' => 1, 'q' => $search]),
      'pagination' => [
        'previous' =>  $this->generateUrl('search', ['id' => $category_id, 'page' => max($page - 1, 1), 'q' => $search]),
        'next' => $this->generateUrl('search', ['id' => $category_id, 'page' =>  min(ceil(count($books) / BookRepository::PAGINATOR_PER_PAGE), $page + 1), 'q' => $search]),
        'page_count' => ceil(count($books) / BookRepository::PAGINATOR_PER_PAGE),
        'current_page' => $page,
        'page_size' => BookRepository::PAGINATOR_PER_PAGE,
      ]
    ]);

    return new JsonResponse(['books' => '', 'query' => $search], Response::HTTP_CREATED);
  }
}
