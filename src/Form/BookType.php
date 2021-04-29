<?php
namespace App\Form;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    private $bookRepository;
    private $categoryRepository;
    public function __construct(BookRepository $bookRepository, CategoryRepository $categoryRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->categoryRepository = $categoryRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [ ])
            ->add('author', TextType::class, [
              'required' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choices' => $this->getCategoryList(),
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('image', FileType::class, [
              'mapped' => false
            ]);
    }

    public function getCategoryList() {
      $categories = $this->categoryRepository->findAll();
      
      return $categories;
    }
}