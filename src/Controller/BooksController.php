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

    #[Route('/books', name: 'books', methods: ["GET"])]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        $books = $entityManager->getRepository(Book::class)->findAll();

        return $this->render('books/index.html.twig', [
            'categories' => $categories,
            'books' => $books
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

          return $this->redirectToRoute('books');
      }
      return $this->render('books/create.html.twig', [
          'bookForm' => $form->createView()
      ]);

    }
}
