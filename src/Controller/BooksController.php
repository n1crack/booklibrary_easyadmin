<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class BooksController extends AbstractController
{

    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    #[Route('/books', name: 'books', methods: ["GET"])]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('books/index.html.twig', [
            'categories' => $categories,
        ]);
    }

  //  #[Route('/books/create', name: 'books.create')]
  //  public function create(): Response
  //  {
  //      $entityManager = $this->getDoctrine()->getManager();
  //      $categories = $entityManager->getRepository(Category::class)->findAll();

  //      return $this->render('books/create.html.twig', [
  //          'categories' => $categories,
  //      ]);
  //  }

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
          $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
          $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
          $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
          $uploadedFile->move(
              $destination,
              $newFilename
          );
          $book->setImage($newFilename);
    
          $em->persist($book);
          $em->flush();
          return new JsonResponse(['status' => 'Book created!'], Response::HTTP_CREATED);
      }
      return $this->render('books/create.html.twig', [
          'bookForm' => $form->createView()
      ]);
//       $name = $request->get('name');
//       $author = $request->get('author');
//       $image = $request->files->get('image');
//       $category_id = $request->get('category');
// 
//       if (empty($name) || empty($author) || empty($category_id)) {
//         throw new NotFoundHttpException('Expecting mandatory parameters!');
//       }
// 
//       if (!$image) {
//           throw new BadRequestHttpException('"thumbnail" is required');
//       }
// 
//       $category = $this->getDoctrine()
//         ->getRepository(Category::class)
//         ->find($category_id);
// 
//       $this->bookRepository->saveBook($name, $author, $image, $category);
// 
//       return new JsonResponse(['status' => 'Book created!'], Response::HTTP_CREATED);
    }
}
