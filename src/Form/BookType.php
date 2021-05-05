<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BookType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [])
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
                'mapped' => false,
            ])
        ;
    }

    public function getCategoryList()
    {
        return $this->categoryRepository->findAll();
    }
}